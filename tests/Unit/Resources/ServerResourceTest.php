<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use PhpDevKits\ForgeSdk\Data\Server;
use PhpDevKits\ForgeSdk\Forge;
use PhpDevKits\ForgeSdk\Requests\Servers\GetServer;
use Saloon\Http\Faking\MockClient;
use Tests\Utils\ServerFixture;

test('get() returns a hydrated Server',
    /**
     * @throws Throwable
     */
    function (): void {
        $token = ($_ENV['FORGE_TEST_TOKEN'] ?? '') ?: 'test-token';
        $org = ($_ENV['FORGE_TEST_ORG_SLUG'] ?? '') ?: 'test-org';
        $serverId = ($_ENV['FORGE_TEST_SERVER_ID'] ?? '') ?: '1';
        $mockClient = new MockClient([
            GetServer::class => new ServerFixture('servers/get'),
        ]);
        $forge = new Forge($token, $org)->withMockClient($mockClient);

        $server = $forge->server($serverId)->get();

        expect($server)->toBeInstanceOf(Server::class)
            ->and($server->id)->toBeString()->not->toBeEmpty()
            ->and($server->name)->toBeString()->not->toBeEmpty();
    });
