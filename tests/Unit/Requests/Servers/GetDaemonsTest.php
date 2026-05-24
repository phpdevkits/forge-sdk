<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Servers;

use PhpDevKits\ForgeSdk\Data\ListDaemonsOptions;
use PhpDevKits\ForgeSdk\Requests\Servers\GetDaemons;
use Saloon\Enums\Method;

test('resolveEndpoint() points at the background-processes collection',
    function (): void {
        expect(new GetDaemons('acme', 42)->resolveEndpoint())->toBe('/orgs/acme/servers/42/background-processes');
    });

test('uses the GET method',
    function (): void {
        expect(new GetDaemons('acme', 42)->getMethod())->toBe(Method::GET);
    });

test('emits options as query parameters',
    function (): void {
        $request = new GetDaemons('acme', 42, new ListDaemonsOptions(size: 5, user: 'forge'));

        expect($request->query()->all())->toBe([
            'page[size]' => 5,
            'filter[user]' => 'forge',
        ]);
    });
