<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use PhpDevKits\ForgeSdk\Data\CreateBackupConfigurationData;
use PhpDevKits\ForgeSdk\Enums\BackupFrequency;
use Tests\Factories\CreateBackupConfigurationDataFactory;

beforeEach(function (): void {
    $this->factory = new CreateBackupConfigurationDataFactory;
});

test('factory produces a usable CreateBackupConfigurationData',
    function (): void {
        $data = $this->factory->make();

        expect($data)->toBeInstanceOf(CreateBackupConfigurationData::class)
            ->and($data->storageProviderId)->toBeInt()
            ->and($data->frequency)->toBe(BackupFrequency::Daily)
            ->and($data->databaseIds)->toBe([1])
            ->and($data->name)->toBeNull();
    });

test('toArray() emits only required fields when all optionals are null',
    function (): void {
        $data = new CreateBackupConfigurationData(
            storageProviderId: 7,
            frequency: BackupFrequency::Daily,
            retention: 7,
            databaseIds: [1, 2],
        );

        expect($data->toArray())->toBe([
            'storage_provider_id' => 7,
            'frequency' => 'daily',
            'retention' => 7,
            'database_ids' => [1, 2],
        ]);
    });

test('toArray() emits name, bucket and directory when set',
    function (): void {
        $data = new CreateBackupConfigurationData(
            storageProviderId: 7,
            frequency: BackupFrequency::Daily,
            retention: 7,
            databaseIds: [1],
            name: 'daily-backup',
            bucket: 'my-bucket',
            directory: 'forge-backups',
        );

        expect($data->toArray())->toBe([
            'storage_provider_id' => 7,
            'frequency' => 'daily',
            'retention' => 7,
            'database_ids' => [1],
            'name' => 'daily-backup',
            'bucket' => 'my-bucket',
            'directory' => 'forge-backups',
        ]);
    });

test('toArray() emits weekly schedule fields (day and time) when set',
    function (): void {
        $data = new CreateBackupConfigurationData(
            storageProviderId: 7,
            frequency: BackupFrequency::Weekly,
            retention: 14,
            databaseIds: [1],
            day: '3',
            time: '02:30',
        );

        expect($data->toArray())->toBe([
            'storage_provider_id' => 7,
            'frequency' => 'weekly',
            'retention' => 14,
            'database_ids' => [1],
            'day' => '3',
            'time' => '02:30',
        ]);
    });

test('toArray() emits cron and notification_email for a custom-schedule payload',
    function (): void {
        $data = new CreateBackupConfigurationData(
            storageProviderId: 7,
            frequency: BackupFrequency::Custom,
            retention: 30,
            databaseIds: [1, 2, 3],
            name: 'nightly',
            cron: '0 3 * * *',
            notificationEmail: 'ops@example.com',
        );

        expect($data->toArray())->toBe([
            'storage_provider_id' => 7,
            'frequency' => 'custom',
            'retention' => 30,
            'database_ids' => [1, 2, 3],
            'name' => 'nightly',
            'cron' => '0 3 * * *',
            'notification_email' => 'ops@example.com',
        ]);
    });
