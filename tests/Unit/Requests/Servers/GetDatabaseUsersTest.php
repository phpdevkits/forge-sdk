<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Servers;

use PhpDevKits\ForgeSdk\Data\ListDatabaseUsersOptions;
use PhpDevKits\ForgeSdk\Requests\Servers\GetDatabaseUsers;
use Saloon\Enums\Method;

test('resolveEndpoint() interpolates the org slug and server id',
    function (): void {
        $request = new GetDatabaseUsers('acme', 42);

        expect($request->resolveEndpoint())->toBe('/orgs/acme/servers/42/database/users');
    });

test('uses the GET method',
    function (): void {
        expect(new GetDatabaseUsers('acme', 42)->getMethod())->toBe(Method::GET);
    });

test('emits no query parameters when no options are supplied',
    function (): void {
        expect(new GetDatabaseUsers('acme', 42)->query()->all())->toBe([]);
    });

test('emits options as query parameters when supplied',
    function (): void {
        $request = new GetDatabaseUsers('acme', 42, new ListDatabaseUsersOptions(size: 5, name: 'forge', status: 'installed'));

        expect($request->query()->all())->toBe([
            'page[size]' => 5,
            'filter[name]' => 'forge',
            'filter[status]' => 'installed',
        ]);
    });
