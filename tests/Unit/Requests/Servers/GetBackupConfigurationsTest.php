<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Servers;

use PhpDevKits\ForgeSdk\Data\ListBackupConfigurationsOptions;
use PhpDevKits\ForgeSdk\Requests\Servers\GetBackupConfigurations;
use Saloon\Enums\Method;

test('resolveEndpoint() interpolates the org slug and server id',
    function (): void {
        $request = new GetBackupConfigurations('acme', 42);

        expect($request->resolveEndpoint())->toBe('/orgs/acme/servers/42/database/backups');
    });

test('uses the GET method',
    function (): void {
        expect(new GetBackupConfigurations('acme', 42)->getMethod())->toBe(Method::GET);
    });

test('emits no query parameters when no options are supplied',
    function (): void {
        expect(new GetBackupConfigurations('acme', 42)->query()->all())->toBe([]);
    });

test('emits options as query parameters when supplied',
    function (): void {
        $request = new GetBackupConfigurations('acme', 42, new ListBackupConfigurationsOptions(size: 5, name: 'daily', status: 'installed'));

        expect($request->query()->all())->toBe([
            'page[size]' => 5,
            'filter[name]' => 'daily',
            'filter[status]' => 'installed',
        ]);
    });
