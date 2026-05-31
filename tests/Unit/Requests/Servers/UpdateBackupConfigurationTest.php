<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Servers;

use PhpDevKits\ForgeSdk\Data\UpdateBackupConfigurationData;
use PhpDevKits\ForgeSdk\Enums\BackupFrequency;
use PhpDevKits\ForgeSdk\Requests\Servers\UpdateBackupConfiguration;
use Saloon\Enums\Method;

test('resolveEndpoint() interpolates the backup configuration id',
    function (): void {
        $data = new UpdateBackupConfigurationData(
            storageProviderId: 7,
            frequency: BackupFrequency::Daily,
            retention: 14,
            databaseIds: [1],
        );

        expect(new UpdateBackupConfiguration('acme', 42, 99, $data)->resolveEndpoint())
            ->toBe('/orgs/acme/servers/42/database/backups/99');
    });

test('uses the PUT method',
    function (): void {
        $data = new UpdateBackupConfigurationData(
            storageProviderId: 7,
            frequency: BackupFrequency::Daily,
            retention: 14,
            databaseIds: [1],
        );

        expect(new UpdateBackupConfiguration('acme', 42, 99, $data)->getMethod())->toBe(Method::PUT);
    });

test('body() reflects the UpdateBackupConfigurationData payload',
    function (): void {
        $data = new UpdateBackupConfigurationData(
            storageProviderId: 7,
            frequency: BackupFrequency::Weekly,
            retention: 14,
            databaseIds: [1, 2],
            name: 'weekly-backup',
            day: '0',
            time: '02:00',
        );

        expect(new UpdateBackupConfiguration('acme', 42, 99, $data)->body()->all())->toBe([
            'storage_provider_id' => 7,
            'frequency' => 'weekly',
            'retention' => 14,
            'database_ids' => [1, 2],
            'name' => 'weekly-backup',
            'day' => '0',
            'time' => '02:00',
        ]);
    });
