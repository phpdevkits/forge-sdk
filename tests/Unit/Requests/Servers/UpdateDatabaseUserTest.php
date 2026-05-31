<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Servers;

use PhpDevKits\ForgeSdk\Data\UpdateDatabaseUserData;
use PhpDevKits\ForgeSdk\Requests\Servers\UpdateDatabaseUser;
use Saloon\Enums\Method;

test('resolveEndpoint() interpolates the org slug, server id and database user id',
    function (): void {
        expect(new UpdateDatabaseUser('acme', 42, 7, new UpdateDatabaseUserData)->resolveEndpoint())
            ->toBe('/orgs/acme/servers/42/database/users/7');
    });

test('uses the PUT method',
    function (): void {
        expect(new UpdateDatabaseUser('acme', 42, 7, new UpdateDatabaseUserData)->getMethod())->toBe(Method::PUT);
    });

test('body() reflects the UpdateDatabaseUserData payload',
    function (): void {
        $data = new UpdateDatabaseUserData(password: 'rotated', databaseIds: [1, 2]);

        expect(new UpdateDatabaseUser('acme', 42, 7, $data)->body()->all())->toBe([
            'password' => 'rotated',
            'database_ids' => [1, 2],
        ]);
    });

test('body() is empty when no fields are set',
    function (): void {
        expect(new UpdateDatabaseUser('acme', 42, 7, new UpdateDatabaseUserData)->body()->all())->toBe([]);
    });
