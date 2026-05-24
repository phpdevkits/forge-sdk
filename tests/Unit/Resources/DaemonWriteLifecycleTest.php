<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use PhpDevKits\ForgeSdk\Data\CreateDaemonData;
use PhpDevKits\ForgeSdk\Data\Daemon;
use PhpDevKits\ForgeSdk\Data\UpdateDaemonData;
use PhpDevKits\ForgeSdk\Enums\DaemonUser;
use PhpDevKits\ForgeSdk\Forge;
use PhpDevKits\ForgeSdk\Requests\Servers\CreateDaemon;
use PhpDevKits\ForgeSdk\Requests\Servers\DeleteDaemon;
use PhpDevKits\ForgeSdk\Requests\Servers\GetDaemon;
use PhpDevKits\ForgeSdk\Requests\Servers\GetDaemonLog;
use PhpDevKits\ForgeSdk\Requests\Servers\GetDaemons;
use PhpDevKits\ForgeSdk\Requests\Servers\SendDaemonAction;
use PhpDevKits\ForgeSdk\Requests\Servers\UpdateDaemon;
use Saloon\Http\Faking\MockClient;
use Tests\Utils\DaemonFixture;
use Throwable;

/*
 * End-to-end daemon lifecycle: create -> list -> get -> update -> restart
 * -> log -> delete, against the configured Forge server (recording) or
 * replayed from committed fixtures otherwise. try/finally guards cleanup so
 * a partway failure during recording doesn't leak a real daemon.
 *
 * The daemon runs `sleep 9999` (harmless, supervisor-managed) and is
 * deleted at the end.
 *
 * Required env vars (recording only): FORGE_TEST_TOKEN,
 * FORGE_TEST_ORG_SLUG, FORGE_TEST_SERVER_ID.
 */
test('lifecycle: create -> list -> get -> update -> restart -> log -> delete',
    /**
     * @throws Throwable
     */
    function (): void {
        $token = ($_ENV['FORGE_TEST_TOKEN'] ?? '') ?: 'test-token';
        $org = ($_ENV['FORGE_TEST_ORG_SLUG'] ?? '') ?: 'test-org';
        $serverId = ($_ENV['FORGE_TEST_SERVER_ID'] ?? '') ?: '1';

        $recording = ! file_exists(__DIR__.'/../../Fixtures/Saloon/daemons/get.json');

        $mockClient = new MockClient([
            CreateDaemon::class => new DaemonFixture('daemons/create'),
            GetDaemons::class => new DaemonFixture('daemons/list'),
            GetDaemon::class => new DaemonFixture('daemons/get'),
            UpdateDaemon::class => new DaemonFixture('daemons/update'),
            SendDaemonAction::class => new DaemonFixture('daemons/action'),
            GetDaemonLog::class => new DaemonFixture('daemons/log'),
            DeleteDaemon::class => new DaemonFixture('daemons/delete'),
        ]);
        $forge = new Forge($token, $org)->withMockClient($mockClient);

        $createdId = null;
        try {
            $created = $forge->server($serverId)->daemons()->create(
                new CreateDaemonData(name: 'sdk-test-worker', command: 'sleep 9999', user: DaemonUser::Forge),
            );
            $createdId = $created->id;

            expect($created)->toBeInstanceOf(Daemon::class)
                ->and($created->id)->toBeString()->not->toBeEmpty();

            // Wait for the daemon's supervisor process to leave the
            // `installing` state before acting on it (restart 500s while
            // installing). A live un-mocked connector observes real state;
            // skipped on replay, where Saloon replays the first recorded get.
            if ($recording) {
                $live = new Forge($token, $org);
                for ($attempt = 0; $attempt < 45; $attempt++) {
                    try {
                        $status = $live->server($serverId)->daemon($createdId)->get()->status;
                        if ($status !== 'installing' && $status !== '') {
                            break;
                        }
                    } catch (Throwable) {
                        // not yet reachable; keep polling
                    }

                    sleep(2);
                }
            }

            $page = $forge->server($serverId)->daemons()->all();
            expect($page->data)->not->toBeEmpty();

            $fetched = $forge->server($serverId)->daemon($createdId)->get();
            expect($fetched)->toBeInstanceOf(Daemon::class);

            // Forge's daemon update 500s with name-only — `config` is
            // effectively required despite the spec marking it optional.
            $forge->server($serverId)->daemon($createdId)->update(new UpdateDaemonData(
                name: 'sdk-test-worker',
                config: "[program:sdk-test-worker]\ncommand=sleep 9999\nuser=forge\nnumprocs=1\nautostart=true\nautorestart=true",
            ));
            $forge->server($serverId)->daemon($createdId)->restart();

            expect($forge->server($serverId)->daemon($createdId)->log())->toBeString();

            $forge->server($serverId)->daemon($createdId)->delete();
            $createdId = null;
        } finally {
            if ($createdId !== null) {
                try {
                    $forge->server($serverId)->daemon($createdId)->delete();
                } catch (Throwable) {
                    // best-effort cleanup; original failure surfaces
                }
            }
        }
    });
