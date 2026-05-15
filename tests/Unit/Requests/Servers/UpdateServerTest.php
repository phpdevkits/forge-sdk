<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Servers;

use PhpDevKits\ForgeSdk\Data\UpdateServerData;
use PhpDevKits\ForgeSdk\Requests\Servers\UpdateServer;
use Saloon\Enums\Method;

test('resolveEndpoint() interpolates the organization slug and server id',
    function (): void {
        $request = new UpdateServer('acme', 42, new UpdateServerData);

        expect($request->resolveEndpoint())->toBe('/orgs/acme/servers/42');
    });

test('uses the PUT method',
    function (): void {
        $request = new UpdateServer('acme', 42, new UpdateServerData);

        expect($request->getMethod())->toBe(Method::PUT);
    });

test('body() reflects only the fields that are set',
    function (): void {
        $request = new UpdateServer('acme', 42, new UpdateServerData(name: 'renamed'));

        expect($request->body()->all())->toBe(['name' => 'renamed']);
    });
