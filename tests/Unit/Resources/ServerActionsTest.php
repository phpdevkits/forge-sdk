<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use PhpDevKits\ForgeSdk\Forge;
use PhpDevKits\ForgeSdk\Requests\Servers\SendServerAction;
use Saloon\Http\Faking\MockClient;
use Tests\Utils\ServerFixture;

test('powerCycle() sends a power-cycle action to /servers/{id}/actions',
    /**
     * @throws Throwable
     */
    function (): void {
        $mockClient = new MockClient([
            SendServerAction::class => new ServerFixture('servers/action-reboot'),
        ]);
        $forge = new Forge('test-token', 'test-org')->withMockClient($mockClient);

        $forge->server(1)->powerCycle();

        $request = $mockClient->getLastPendingRequest();
        $body = $request?->body()->all() ?? [];

        expect($body)->toBe(['action' => 'power-cycle']);
    });
