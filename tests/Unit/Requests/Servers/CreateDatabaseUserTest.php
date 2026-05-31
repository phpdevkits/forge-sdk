<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Servers;

use PhpDevKits\ForgeSdk\Data\CreateDatabaseUserData;
use PhpDevKits\ForgeSdk\Requests\Servers\CreateDatabaseUser;
use Saloon\Enums\Method;

test('resolveEndpoint() interpolates the org slug and server id',
    function (): void {
        $data = new CreateDatabaseUserData(name: 'james', password: 'secret');

        expect(new CreateDatabaseUser('acme', 42, $data)->resolveEndpoint())->toBe('/orgs/acme/servers/42/database/users');
    });

test('uses the POST method',
    function (): void {
        expect(new CreateDatabaseUser('acme', 42, new CreateDatabaseUserData(name: 'james', password: 'secret'))->getMethod())->toBe(Method::POST);
    });

test('body() reflects the CreateDatabaseUserData payload',
    function (): void {
        $data = new CreateDatabaseUserData(
            name: 'james',
            password: 'secret',
            readOnly: true,
            databaseIds: [1, 2, 3],
        );

        expect(new CreateDatabaseUser('acme', 42, $data)->body()->all())->toBe([
            'name' => 'james',
            'password' => 'secret',
            'read_only' => true,
            'database_ids' => [1, 2, 3],
        ]);
    });

test('body() omits null optional fields',
    function (): void {
        expect(new CreateDatabaseUser('acme', 42, new CreateDatabaseUserData(name: 'james', password: 'secret'))->body()->all())->toBe([
            'name' => 'james',
            'password' => 'secret',
        ]);
    });
