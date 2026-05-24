<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Servers;

use PhpDevKits\ForgeSdk\Data\ListSitesOptions;
use PhpDevKits\ForgeSdk\Requests\Servers\GetSites;
use Saloon\Enums\Method;

test('resolveEndpoint() interpolates the org slug and server id',
    function (): void {
        expect(new GetSites('acme', 42)->resolveEndpoint())->toBe('/orgs/acme/servers/42/sites');
    });

test('uses the GET method',
    function (): void {
        expect(new GetSites('acme', 42)->getMethod())->toBe(Method::GET);
    });

test('emits options as query parameters',
    function (): void {
        $request = new GetSites('acme', 42, new ListSitesOptions(size: 5, name: 'example.com'));

        expect($request->query()->all())->toBe([
            'page[size]' => 5,
            'filter[name]' => 'example.com',
        ]);
    });
