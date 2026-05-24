<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Servers;

use PhpDevKits\ForgeSdk\Data\ListMonitorsOptions;
use PhpDevKits\ForgeSdk\Requests\Servers\GetMonitors;
use Saloon\Enums\Method;

test('resolveEndpoint() interpolates the org slug and server id',
    function (): void {
        $request = new GetMonitors('acme', 42);

        expect($request->resolveEndpoint())->toBe('/orgs/acme/servers/42/monitors');
    });

test('uses the GET method',
    function (): void {
        expect(new GetMonitors('acme', 42)->getMethod())->toBe(Method::GET);
    });

test('emits options as query parameters when supplied',
    function (): void {
        $request = new GetMonitors('acme', 42, new ListMonitorsOptions(size: 5, type: 'cpu_load'));

        expect($request->query()->all())->toBe([
            'page[size]' => 5,
            'filter[type]' => 'cpu_load',
        ]);
    });
