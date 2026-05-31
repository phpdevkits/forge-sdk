<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use InvalidArgumentException;
use PhpDevKits\ForgeSdk\Data\BackupConfiguration;
use Tests\Factories\BackupConfigurationFactory;

beforeEach(function (): void {
    $this->factory = new BackupConfigurationFactory;
});

test('factory produces a usable BackupConfiguration',
    function (): void {
        $config = $this->factory->make();

        expect($config)->toBeInstanceOf(BackupConfiguration::class)
            ->and($config->id)->toBeString()->not->toBeEmpty()
            ->and($config->databaseIds)->toBeArray()->not->toBeEmpty()
            ->and($config->retention)->toBeInt();
    });

test('::from() hydrates a full JSON:API resource object',
    function (): void {
        $config = BackupConfiguration::from([
            'id' => '42',
            'type' => 'backupConfigurations',
            'attributes' => [
                'name' => 'daily-backup',
                'storage_provider_id' => 7,
                'provider' => 's3',
                'bucket' => 'my-bucket',
                'directory' => 'forge-backups',
                'schedule' => 'daily',
                'displayable_schedule' => 'Every day at 02:00',
                'next_run_time' => '2026-06-01 02:00:00',
                'status' => 'installed',
                'day_of_week' => null,
                'time' => '02:00',
                'cron_schedule' => null,
                'database_ids' => [1, 2, 3],
                'retention' => 7,
                'notify_email' => 'ops@example.com',
            ],
        ]);

        expect($config->id)->toBe('42')
            ->and($config->name)->toBe('daily-backup')
            ->and($config->storageProviderId)->toBe(7)
            ->and($config->provider)->toBe('s3')
            ->and($config->bucket)->toBe('my-bucket')
            ->and($config->directory)->toBe('forge-backups')
            ->and($config->schedule)->toBe('daily')
            ->and($config->displayableSchedule)->toBe('Every day at 02:00')
            ->and($config->nextRunTime)->toBe('2026-06-01 02:00:00')
            ->and($config->status)->toBe('installed')
            ->and($config->dayOfWeek)->toBeNull()
            ->and($config->time)->toBe('02:00')
            ->and($config->cronSchedule)->toBeNull()
            ->and($config->databaseIds)->toBe([1, 2, 3])
            ->and($config->retention)->toBe(7)
            ->and($config->notifyEmail)->toBe('ops@example.com');
    });

test('::from() accepts null storage_provider_id, bucket and notify_email',
    function (): void {
        $config = BackupConfiguration::from([
            'id' => '1',
            'attributes' => [
                'name' => 'custom',
                'storage_provider_id' => null,
                'provider' => 'custom',
                'bucket' => null,
                'directory' => '/var/backups',
                'schedule' => 'custom',
                'displayable_schedule' => 'Custom cron',
                'next_run_time' => '2026-06-01 03:00:00',
                'status' => 'installed',
                'day_of_week' => 3,
                'time' => null,
                'cron_schedule' => '0 3 * * *',
                'database_ids' => [9],
                'retention' => 14,
                'notify_email' => null,
            ],
        ]);

        expect($config->storageProviderId)->toBeNull()
            ->and($config->bucket)->toBeNull()
            ->and($config->dayOfWeek)->toBe(3)
            ->and($config->cronSchedule)->toBe('0 3 * * *')
            ->and($config->notifyEmail)->toBeNull();
    });

test('::from() throws when id is missing',
    function (): void {
        BackupConfiguration::from(['attributes' => []]);
    })->throws(InvalidArgumentException::class, 'missing the `id` field');

test('::from() throws when attributes is missing',
    function (): void {
        BackupConfiguration::from(['id' => '1']);
    })->throws(InvalidArgumentException::class, 'missing the `attributes` object');

test('::from() throws when a required string field is missing',
    function (): void {
        BackupConfiguration::from(['id' => '1', 'attributes' => []]);
    })->throws(InvalidArgumentException::class, '`attributes.name` must be a string');

test('::from() throws when provider is the wrong type',
    function (): void {
        BackupConfiguration::from(['id' => '1', 'attributes' => [
            'name' => 'daily',
            'storage_provider_id' => 1,
            'provider' => 123,
        ]]);
    })->throws(InvalidArgumentException::class, '`attributes.provider` must be a string');

test('::from() throws when storage_provider_id is the wrong type',
    function (): void {
        BackupConfiguration::from(['id' => '1', 'attributes' => [
            'name' => 'daily',
            'storage_provider_id' => 'seven',
            'provider' => 's3',
        ]]);
    })->throws(InvalidArgumentException::class, '`attributes.storage_provider_id` must be an integer or null');

test('::from() throws when retention is missing',
    function (): void {
        BackupConfiguration::from(['id' => '1', 'attributes' => [
            'name' => 'daily',
            'storage_provider_id' => 1,
            'provider' => 's3',
            'bucket' => 'b',
            'directory' => 'd',
            'schedule' => 'daily',
            'displayable_schedule' => 'x',
            'next_run_time' => 'n',
            'status' => 'installed',
            'day_of_week' => null,
            'time' => null,
            'cron_schedule' => null,
            'database_ids' => [1],
            'notify_email' => null,
        ]]);
    })->throws(InvalidArgumentException::class, '`attributes.retention` must be an integer');

test('::from() throws when database_ids is not an array',
    function (): void {
        BackupConfiguration::from(['id' => '1', 'attributes' => [
            'name' => 'daily',
            'storage_provider_id' => 1,
            'provider' => 's3',
            'bucket' => 'b',
            'directory' => 'd',
            'schedule' => 'daily',
            'displayable_schedule' => 'x',
            'next_run_time' => 'n',
            'status' => 'installed',
            'day_of_week' => null,
            'time' => null,
            'cron_schedule' => null,
            'database_ids' => 'all',
            'retention' => 7,
            'notify_email' => null,
        ]]);
    })->throws(InvalidArgumentException::class, '`attributes.database_ids` must be an array');

test('::from() throws when database_ids contains a non-integer',
    function (): void {
        BackupConfiguration::from(['id' => '1', 'attributes' => [
            'name' => 'daily',
            'storage_provider_id' => 1,
            'provider' => 's3',
            'bucket' => 'b',
            'directory' => 'd',
            'schedule' => 'daily',
            'displayable_schedule' => 'x',
            'next_run_time' => 'n',
            'status' => 'installed',
            'day_of_week' => null,
            'time' => null,
            'cron_schedule' => null,
            'database_ids' => [1, '2', 3],
            'retention' => 7,
            'notify_email' => null,
        ]]);
    })->throws(InvalidArgumentException::class, '`attributes.database_ids` must contain only integers');

test('jsonSerialize() emits snake_case keys with all fields',
    function (): void {
        $config = $this->factory->make([
            'id' => '99',
            'name' => 'daily-backup',
            'storageProviderId' => 7,
            'provider' => 's3',
            'bucket' => 'my-bucket',
            'directory' => 'forge-backups',
            'schedule' => 'daily',
            'displayableSchedule' => 'Every day at 02:00',
            'nextRunTime' => '2026-06-01 02:00:00',
            'status' => 'installed',
            'dayOfWeek' => null,
            'time' => '02:00',
            'cronSchedule' => null,
            'databaseIds' => [1, 2],
            'retention' => 7,
            'notifyEmail' => 'ops@example.com',
        ]);

        expect($config->jsonSerialize())->toBe([
            'id' => '99',
            'name' => 'daily-backup',
            'storage_provider_id' => 7,
            'provider' => 's3',
            'bucket' => 'my-bucket',
            'directory' => 'forge-backups',
            'schedule' => 'daily',
            'displayable_schedule' => 'Every day at 02:00',
            'next_run_time' => '2026-06-01 02:00:00',
            'status' => 'installed',
            'day_of_week' => null,
            'time' => '02:00',
            'cron_schedule' => null,
            'database_ids' => [1, 2],
            'retention' => 7,
            'notify_email' => 'ops@example.com',
        ]);
    });
