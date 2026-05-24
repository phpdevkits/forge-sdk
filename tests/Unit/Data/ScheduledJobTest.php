<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use DateTimeImmutable;
use InvalidArgumentException;
use PhpDevKits\ForgeSdk\Data\ScheduledJob;
use Tests\Factories\ScheduledJobFactory;

beforeEach(function (): void {
    $this->factory = new ScheduledJobFactory;

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    $this->attrs = static fn (array $overrides = []): array => array_merge([
        'name' => 'My scheduled job',
        'command' => 'php artisan schedule:run',
        'status' => 'installed',
        'user' => 'forge',
        'frequency' => 'hourly',
        'cron' => '0 * * * *',
        'next_run_time' => '2026-01-01 01:00:00',
        'created_at' => '2026-01-01T00:00:00Z',
        'updated_at' => '2026-01-02T00:00:00Z',
    ], $overrides);
});

test('factory produces a usable ScheduledJob',
    function (): void {
        expect($this->factory->make())->toBeInstanceOf(ScheduledJob::class);
    });

test('::from() hydrates a full scheduled job',
    function (): void {
        $job = ScheduledJob::from(['id' => '7', 'type' => 'scheduledJobs', 'attributes' => ($this->attrs)()]);

        expect($job->id)->toBe('7')
            ->and($job->name)->toBe('My scheduled job')
            ->and($job->command)->toBe('php artisan schedule:run')
            ->and($job->status)->toBe('installed')
            ->and($job->user)->toBe('forge')
            ->and($job->frequency)->toBe('hourly')
            ->and($job->cron)->toBe('0 * * * *')
            ->and($job->nextRunTime)->toBe('2026-01-01 01:00:00')
            ->and($job->createdAt?->format('Y-m-d'))->toBe('2026-01-01')
            ->and($job->updatedAt?->format('Y-m-d'))->toBe('2026-01-02');
    });

test('::from() accepts a null name',
    function (): void {
        expect(ScheduledJob::from(['id' => '1', 'attributes' => ($this->attrs)(['name' => null])])->name)->toBeNull();
    });

test('::from() accepts null timestamps',
    function (): void {
        $job = ScheduledJob::from(['id' => '1', 'attributes' => ($this->attrs)(['created_at' => null, 'updated_at' => null])]);

        expect($job->createdAt)->toBeNull()
            ->and($job->updatedAt)->toBeNull();
    });

test('::from() throws when id is missing',
    function (): void {
        ScheduledJob::from(['attributes' => []]);
    })->throws(InvalidArgumentException::class, 'missing the `id` field');

test('::from() throws when attributes is missing',
    function (): void {
        ScheduledJob::from(['id' => '1']);
    })->throws(InvalidArgumentException::class, 'missing the `attributes` object');

test('::from() throws when a required string field is missing',
    function (): void {
        $attrs = ($this->attrs)();
        unset($attrs['command']);
        ScheduledJob::from(['id' => '1', 'attributes' => $attrs]);
    })->throws(InvalidArgumentException::class, '`attributes.command` must be a string');

test('::from() throws when name is the wrong type',
    function (): void {
        ScheduledJob::from(['id' => '1', 'attributes' => ($this->attrs)(['name' => 42])]);
    })->throws(InvalidArgumentException::class, '`attributes.name` must be a string or null');

test('::from() throws when created_at is the wrong type',
    function (): void {
        ScheduledJob::from(['id' => '1', 'attributes' => ($this->attrs)(['created_at' => 42])]);
    })->throws(InvalidArgumentException::class, '`attributes.created_at` must be a string or null');

test('::from() throws when created_at is malformed',
    function (): void {
        ScheduledJob::from(['id' => '1', 'attributes' => ($this->attrs)(['created_at' => 'not-a-date'])]);
    })->throws(InvalidArgumentException::class, '`attributes.created_at` is not a valid date-time');

test('jsonSerialize() emits snake_case with ATOM dates',
    function (): void {
        $job = $this->factory->make([
            'id' => '9',
            'createdAt' => new DateTimeImmutable('2026-01-01T00:00:00+00:00'),
            'updatedAt' => new DateTimeImmutable('2026-01-02T00:00:00+00:00'),
        ]);

        $json = $job->jsonSerialize();

        expect($json['id'])->toBe('9')
            ->and($json)->toHaveKey('next_run_time')
            ->and($json['created_at'])->toBe('2026-01-01T00:00:00+00:00')
            ->and($json['updated_at'])->toBe('2026-01-02T00:00:00+00:00');
    });

test('jsonSerialize() emits null timestamps as null',
    function (): void {
        $job = $this->factory->make(['createdAt' => null, 'updatedAt' => null]);

        $json = $job->jsonSerialize();

        expect($json['created_at'])->toBeNull()
            ->and($json['updated_at'])->toBeNull();
    });
