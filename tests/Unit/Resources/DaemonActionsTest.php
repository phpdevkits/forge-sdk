<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use PhpDevKits\ForgeSdk\Forge;
use PhpDevKits\ForgeSdk\Requests\Servers\SendDaemonAction;
use Saloon\Http\Faking\MockClient;
use Tests\Utils\DaemonFixture;

test('restart/stop/start/emptyLog each POST an action to the daemon',
    /**
     * @throws Throwable
     */
    function (): void {
        $token = ($_ENV['FORGE_TEST_TOKEN'] ?? '') ?: 'test-token';
        $org = ($_ENV['FORGE_TEST_ORG_SLUG'] ?? '') ?: 'test-org';
        $serverId = ($_ENV['FORGE_TEST_SERVER_ID'] ?? '') ?: '1';
        $daemonId = ($_ENV['FORGE_TEST_DAEMON_ID'] ?? '') ?: '1';
        $mockClient = new MockClient([
            SendDaemonAction::class => new DaemonFixture('daemons/action'),
        ]);
        $forge = new Forge($token, $org)->withMockClient($mockClient);
        $daemon = $forge->server($serverId)->daemon($daemonId);

        $daemon->restart();
        $daemon->stop();
        $daemon->start();
        $daemon->emptyLog();

        $body = $mockClient->getLastPendingRequest()?->body()->all() ?? [];

        expect($body)->toBe(['action' => 'empty-log']);
    });
