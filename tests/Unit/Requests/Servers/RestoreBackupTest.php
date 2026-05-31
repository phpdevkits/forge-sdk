<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Servers;

use PhpDevKits\ForgeSdk\Data\RestoreBackupData;
use PhpDevKits\ForgeSdk\Requests\Servers\RestoreBackup;
use Saloon\Enums\Method;

test('resolveEndpoint() posts to the restores sub-collection',
    function (): void {
        $data = new RestoreBackupData(databaseId: 5);

        expect(new RestoreBackup('acme', 42, 7, 99, $data)->resolveEndpoint())
            ->toBe('/orgs/acme/servers/42/database/backups/7/instances/99/restores');
    });

test('uses the POST method',
    function (): void {
        $data = new RestoreBackupData(databaseId: 5);

        expect(new RestoreBackup('acme', 42, 7, 99, $data)->getMethod())->toBe(Method::POST);
    });

test('body() reflects the RestoreBackupData payload',
    function (): void {
        $data = new RestoreBackupData(databaseId: 12345);

        expect(new RestoreBackup('acme', 42, 7, 99, $data)->body()->all())->toBe([
            'database_id' => 12345,
        ]);
    });
