<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use PhpDevKits\ForgeSdk\Data\CreateServerData;
use PhpDevKits\ForgeSdk\Data\HetznerServerConfig;
use PhpDevKits\ForgeSdk\Data\Server;
use PhpDevKits\ForgeSdk\Data\UpdateServerData;
use PhpDevKits\ForgeSdk\Enums\PhpVersion;
use PhpDevKits\ForgeSdk\Enums\ServerType;
use PhpDevKits\ForgeSdk\Enums\UbuntuVersion;
use PhpDevKits\ForgeSdk\Forge;
use PhpDevKits\ForgeSdk\Requests\Servers\CreateServer;
use PhpDevKits\ForgeSdk\Requests\Servers\DeleteServer;
use PhpDevKits\ForgeSdk\Requests\Servers\SendServerAction;
use PhpDevKits\ForgeSdk\Requests\Servers\UpdateServer;
use Saloon\Http\Faking\MockClient;
use Tests\Utils\ServerFixture;
use Throwable;

/*
 * One end-to-end test that exercises every write path against a real Hetzner
 * server (during recording) and replays the recorded fixtures afterwards. The
 * try/finally guarantees the real server is deleted even if the test throws
 * partway through — recording-mode crashes can otherwise leak billed infra.
 *
 * Required env vars (recording only — replay needs nothing):
 *   FORGE_TEST_TOKEN, FORGE_TEST_ORG_SLUG, FORGE_TEST_HETZNER_CREDENTIAL_ID,
 *   FORGE_TEST_HETZNER_REGION, FORGE_TEST_HETZNER_SIZE
 */
test('lifecycle: create -> update -> reboot -> delete',
    /**
     * @throws Throwable
     */
    function (): void {
        $token = ($_ENV['FORGE_TEST_TOKEN'] ?? '') ?: 'test-token';
        $org = ($_ENV['FORGE_TEST_ORG_SLUG'] ?? '') ?: 'test-org';
        $credentialId = (int) ($_ENV['FORGE_TEST_HETZNER_CREDENTIAL_ID'] ?? 1);
        $region = ($_ENV['FORGE_TEST_HETZNER_REGION'] ?? '') ?: 'fsn1';
        $size = ($_ENV['FORGE_TEST_HETZNER_SIZE'] ?? '') ?: 'cax11';
        $networkId = (int) ($_ENV['FORGE_TEST_HETZNER_NETWORK_ID'] ?? 1);

        $mockClient = new MockClient([
            CreateServer::class => new ServerFixture('servers/create'),
            UpdateServer::class => new ServerFixture('servers/update'),
            SendServerAction::class => new ServerFixture('servers/action-reboot'),
            DeleteServer::class => new ServerFixture('servers/delete'),
        ]);
        $forge = new Forge($token, $org)->withMockClient($mockClient);

        $createdId = null;
        try {
            $createData = new CreateServerData(
                name: 'sdk-test',
                provider: 'hetzner',
                type: ServerType::App,
                ubuntuVersion: UbuntuVersion::Ubuntu2404,
                credentialId: $credentialId,
                phpVersion: PhpVersion::Php84,
                hetzner: new HetznerServerConfig(regionId: $region, sizeId: $size, networkId: $networkId),
            );

            $created = $forge->servers()->create($createData);
            $createdId = $created->id;

            expect($created)->toBeInstanceOf(Server::class)
                ->and($created->id)->toBeString()->not->toBeEmpty()
                ->and($created->provider)->toBeString()->not->toBeEmpty();

            $updated = $forge->server($createdId)->update(new UpdateServerData(name: 'sdk-test-renamed'));

            expect($updated)->toBeInstanceOf(Server::class)
                ->and($updated->name)->toBeString()->not->toBeEmpty();

            $forge->server($createdId)->reboot();

            $forge->server($createdId)->delete();
            $createdId = null;
        } finally {
            // Safety net: if any step above threw, the server is still real
            // and billable — delete it best-effort before exiting.
            if ($createdId !== null) {
                try {
                    $forge->server($createdId)->delete();
                } catch (Throwable) {
                    // Test already failed; swallow the cleanup error so the
                    // original assertion failure surfaces.
                }
            }
        }
    });
