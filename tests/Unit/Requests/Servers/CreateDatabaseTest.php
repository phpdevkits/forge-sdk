<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Servers;

use PhpDevKits\ForgeSdk\Data\CreateDatabaseData;
use PhpDevKits\ForgeSdk\Requests\Servers\CreateDatabase;
use Saloon\Enums\Method;

test('resolveEndpoint() interpolates the org slug and server id',
    function (): void {
        $data = new CreateDatabaseData(name: 'forge');

        expect(new CreateDatabase('acme', 42, $data)->resolveEndpoint())->toBe('/orgs/acme/servers/42/database/schemas');
    });

test('uses the POST method',
    function (): void {
        expect(new CreateDatabase('acme', 42, new CreateDatabaseData(name: 'forge'))->getMethod())->toBe(Method::POST);
    });

test('body() reflects the CreateDatabaseData payload',
    function (): void {
        $data = new CreateDatabaseData(name: 'forge', user: 'james', password: 'secret');

        expect(new CreateDatabase('acme', 42, $data)->body()->all())->toBe([
            'name' => 'forge',
            'user' => 'james',
            'password' => 'secret',
        ]);
    });

test('body() omits null optional fields',
    function (): void {
        expect(new CreateDatabase('acme', 42, new CreateDatabaseData(name: 'forge'))->body()->all())->toBe([
            'name' => 'forge',
        ]);
    });
