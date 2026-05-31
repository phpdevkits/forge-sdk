<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Servers;

use PhpDevKits\ForgeSdk\Data\ListBackupInstancesOptions;
use PhpDevKits\ForgeSdk\Requests\Servers\GetBackupInstances;
use Saloon\Enums\Method;

test('resolveEndpoint() points at the instances collection',
    function (): void {
        expect(new GetBackupInstances('acme', 42, 7)->resolveEndpoint())
            ->toBe('/orgs/acme/servers/42/database/backups/7/instances');
    });

test('uses the GET method',
    function (): void {
        expect(new GetBackupInstances('acme', 42, 7)->getMethod())->toBe(Method::GET);
    });

test('emits options as query parameters',
    function (): void {
        $request = new GetBackupInstances('acme', 42, 7, new ListBackupInstancesOptions(size: 5, status: 'finished'));

        expect($request->query()->all())->toBe([
            'page[size]' => 5,
            'filter[status]' => 'finished',
        ]);
    });
