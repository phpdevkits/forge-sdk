<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Servers;

use PhpDevKits\ForgeSdk\Requests\Servers\GetServer;

test('resolveEndpoint() interpolates the organization slug and server id',
    function (): void {
        $request = new GetServer('acme', 42);

        expect($request->resolveEndpoint())->toBe('/orgs/acme/servers/42');
    });

test('resolveEndpoint() accepts a string server id',
    function (): void {
        $request = new GetServer('acme', 'production-web-01');

        expect($request->resolveEndpoint())->toBe('/orgs/acme/servers/production-web-01');
    });
