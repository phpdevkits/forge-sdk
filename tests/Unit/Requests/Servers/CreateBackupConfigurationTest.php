<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Servers;

use PhpDevKits\ForgeSdk\Data\CreateBackupConfigurationData;
use PhpDevKits\ForgeSdk\Enums\BackupFrequency;
use PhpDevKits\ForgeSdk\Requests\Servers\CreateBackupConfiguration;
use Saloon\Enums\Method;

test('resolveEndpoint() posts to the backup configurations collection',
    function (): void {
        $data = new CreateBackupConfigurationData(
            storageProviderId: 7,
            frequency: BackupFrequency::Daily,
            retention: 7,
            databaseIds: [1],
        );

        expect(new CreateBackupConfiguration('acme', 42, $data)->resolveEndpoint())->toBe('/orgs/acme/servers/42/database/backups');
    });

test('uses the POST method',
    function (): void {
        $data = new CreateBackupConfigurationData(
            storageProviderId: 7,
            frequency: BackupFrequency::Daily,
            retention: 7,
            databaseIds: [1],
        );

        expect(new CreateBackupConfiguration('acme', 42, $data)->getMethod())->toBe(Method::POST);
    });

test('body() reflects a minimal CreateBackupConfigurationData payload',
    function (): void {
        $data = new CreateBackupConfigurationData(
            storageProviderId: 7,
            frequency: BackupFrequency::Daily,
            retention: 7,
            databaseIds: [1, 2],
        );

        expect(new CreateBackupConfiguration('acme', 42, $data)->body()->all())->toBe([
            'storage_provider_id' => 7,
            'frequency' => 'daily',
            'retention' => 7,
            'database_ids' => [1, 2],
        ]);
    });

test('body() reflects a full custom-cron payload',
    function (): void {
        $data = new CreateBackupConfigurationData(
            storageProviderId: 9,
            frequency: BackupFrequency::Custom,
            retention: 30,
            databaseIds: [1, 2, 3],
            name: 'nightly',
            bucket: 'my-bucket',
            directory: 'forge-backups',
            cron: '0 3 * * *',
            notificationEmail: 'ops@example.com',
        );

        expect(new CreateBackupConfiguration('acme', 42, $data)->body()->all())->toBe([
            'storage_provider_id' => 9,
            'frequency' => 'custom',
            'retention' => 30,
            'database_ids' => [1, 2, 3],
            'name' => 'nightly',
            'bucket' => 'my-bucket',
            'directory' => 'forge-backups',
            'cron' => '0 3 * * *',
            'notification_email' => 'ops@example.com',
        ]);
    });
