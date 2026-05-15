<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Servers;

use PhpDevKits\ForgeSdk\Requests\Servers\DeleteServer;
use Saloon\Enums\Method;

test('resolveEndpoint() interpolates the organization slug and server id',
    function (): void {
        $request = new DeleteServer('acme', 42);

        expect($request->resolveEndpoint())->toBe('/orgs/acme/servers/42');
    });

test('uses the DELETE method',
    function (): void {
        $request = new DeleteServer('acme', 42);

        expect($request->getMethod())->toBe(Method::DELETE);
    });
