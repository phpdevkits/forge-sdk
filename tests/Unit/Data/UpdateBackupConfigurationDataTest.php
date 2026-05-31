<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use PhpDevKits\ForgeSdk\Data\UpdateBackupConfigurationData;
use PhpDevKits\ForgeSdk\Enums\BackupFrequency;
use Tests\Factories\UpdateBackupConfigurationDataFactory;

beforeEach(function (): void {
    $this->factory = new UpdateBackupConfigurationDataFactory;
});

test('factory produces a usable UpdateBackupConfigurationData',
    function (): void {
        $data = $this->factory->make();

        expect($data)->toBeInstanceOf(UpdateBackupConfigurationData::class)
            ->and($data->storageProviderId)->toBeInt()
            ->and($data->frequency)->toBe(BackupFrequency::Daily)
            ->and($data->retention)->toBe(14)
            ->and($data->databaseIds)->toBe([1]);
    });

test('toArray() emits only required fields when all optionals are null',
    function (): void {
        $data = new UpdateBackupConfigurationData(
            storageProviderId: 7,
            frequency: BackupFrequency::Daily,
            retention: 14,
            databaseIds: [5],
        );

        expect($data->toArray())->toBe([
            'storage_provider_id' => 7,
            'frequency' => 'daily',
            'retention' => 14,
            'database_ids' => [5],
        ]);
    });

test('toArray() emits all schedule and metadata fields when set',
    function (): void {
        $data = new UpdateBackupConfigurationData(
            storageProviderId: 9,
            frequency: BackupFrequency::Custom,
            retention: 30,
            databaseIds: [1, 2],
            name: 'updated-backup',
            bucket: 'new-bucket',
            directory: 'forge/v2',
            day: '0',
            time: '04:00',
            cron: '0 4 * * 0',
            notificationEmail: 'team@example.com',
        );

        expect($data->toArray())->toBe([
            'storage_provider_id' => 9,
            'frequency' => 'custom',
            'retention' => 30,
            'database_ids' => [1, 2],
            'name' => 'updated-backup',
            'bucket' => 'new-bucket',
            'directory' => 'forge/v2',
            'day' => '0',
            'time' => '04:00',
            'cron' => '0 4 * * 0',
            'notification_email' => 'team@example.com',
        ]);
    });
