<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use PhpDevKits\ForgeSdk\Data\Daemon;
use PhpDevKits\ForgeSdk\Forge;
use PhpDevKits\ForgeSdk\Requests\Servers\GetDaemon;
use PhpDevKits\ForgeSdk\Requests\Servers\GetDaemonLog;
use Saloon\Http\Faking\MockClient;
use Tests\Utils\DaemonFixture;

test('get() returns a hydrated Daemon',
    /**
     * @throws Throwable
     */
    function (): void {
        $token = ($_ENV['FORGE_TEST_TOKEN'] ?? '') ?: 'test-token';
        $org = ($_ENV['FORGE_TEST_ORG_SLUG'] ?? '') ?: 'test-org';
        $serverId = ($_ENV['FORGE_TEST_SERVER_ID'] ?? '') ?: '1';
        $daemonId = ($_ENV['FORGE_TEST_DAEMON_ID'] ?? '') ?: '1';
        $mockClient = new MockClient([
            GetDaemon::class => new DaemonFixture('daemons/get'),
        ]);
        $forge = new Forge($token, $org)->withMockClient($mockClient);

        $daemon = $forge->server($serverId)->daemon($daemonId)->get();

        expect($daemon)->toBeInstanceOf(Daemon::class)
            ->and($daemon->id)->toBeString()->not->toBeEmpty()
            ->and($daemon->command)->toBeString()->not->toBeEmpty();
    });

test('log() returns the raw log content string',
    /**
     * @throws Throwable
     */
    function (): void {
        $token = ($_ENV['FORGE_TEST_TOKEN'] ?? '') ?: 'test-token';
        $org = ($_ENV['FORGE_TEST_ORG_SLUG'] ?? '') ?: 'test-org';
        $serverId = ($_ENV['FORGE_TEST_SERVER_ID'] ?? '') ?: '1';
        $daemonId = ($_ENV['FORGE_TEST_DAEMON_ID'] ?? '') ?: '1';
        $mockClient = new MockClient([
            GetDaemonLog::class => new DaemonFixture('daemons/log'),
        ]);
        $forge = new Forge($token, $org)->withMockClient($mockClient);

        $log = $forge->server($serverId)->daemon($daemonId)->log();

        expect($log)->toBeString();
    });
