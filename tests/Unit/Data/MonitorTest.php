<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use DateTimeImmutable;
use InvalidArgumentException;
use PhpDevKits\ForgeSdk\Data\Monitor;
use Tests\Factories\MonitorFactory;

beforeEach(function (): void {
    $this->factory = new MonitorFactory;
});

test('factory produces a usable Monitor',
    function (): void {
        $monitor = $this->factory->make();

        expect($monitor)->toBeInstanceOf(Monitor::class)
            ->and($monitor->id)->toBeString()->not->toBeEmpty()
            ->and($monitor->threshold)->toBeFloat()
            ->and($monitor->createdAt)->toBeInstanceOf(DateTimeImmutable::class);
    });

test('::from() hydrates a full JSON:API resource object',
    function (): void {
        $monitor = Monitor::from([
            'id' => '17',
            'type' => 'monitors',
            'attributes' => [
                'type' => 'cpu_load',
                'operator' => 'gte',
                'threshold' => 90,
                'minutes' => 5,
                'notify' => 'taylor@laravel.com',
                'status' => 'installed',
                'state' => 'OK',
                'state_changed_at' => '2026-04-15T09:00:00Z',
                'created_at' => '2026-04-01T00:00:00Z',
                'updated_at' => '2026-05-01T00:00:00Z',
            ],
        ]);

        expect($monitor->id)->toBe('17')
            ->and($monitor->type)->toBe('cpu_load')
            ->and($monitor->operator)->toBe('gte')
            ->and($monitor->threshold)->toBe(90.0)
            ->and($monitor->minutes)->toBe(5)
            ->and($monitor->notify)->toBe('taylor@laravel.com')
            ->and($monitor->status)->toBe('installed')
            ->and($monitor->state)->toBe('OK')
            ->and($monitor->stateChangedAt?->format('Y-m-d'))->toBe('2026-04-15')
            ->and($monitor->createdAt->format('Y-m-d'))->toBe('2026-04-01');
    });

test('::from() accepts null minutes and null state_changed_at',
    function (): void {
        $monitor = Monitor::from([
            'id' => '1',
            'attributes' => [
                'type' => 'disk',
                'operator' => 'lte',
                'threshold' => 10.5,
                'minutes' => null,
                'notify' => 'ops@example.com',
                'status' => 'installed',
                'state' => 'UNKNOWN',
                'state_changed_at' => null,
                'created_at' => '2026-01-01T00:00:00Z',
                'updated_at' => '2026-01-01T00:00:00Z',
            ],
        ]);

        expect($monitor->minutes)->toBeNull()
            ->and($monitor->stateChangedAt)->toBeNull()
            ->and($monitor->threshold)->toBe(10.5);
    });

test('::from() throws when id is missing',
    function (): void {
        Monitor::from(['attributes' => []]);
    })->throws(InvalidArgumentException::class, 'missing the `id` field');

test('::from() throws when attributes is missing',
    function (): void {
        Monitor::from(['id' => '1']);
    })->throws(InvalidArgumentException::class, 'missing the `attributes` object');

test('::from() throws when a required string field is missing',
    function (): void {
        Monitor::from(['id' => '1', 'attributes' => []]);
    })->throws(InvalidArgumentException::class, '`attributes.type` must be a string');

test('::from() throws when threshold is not a number',
    function (): void {
        Monitor::from(['id' => '1', 'attributes' => [
            'type' => 'cpu_load', 'operator' => 'gte',
            'threshold' => '90',
            'notify' => 'a@b.c', 'status' => 'installed', 'state' => 'OK',
            'created_at' => '2026-01-01T00:00:00Z', 'updated_at' => '2026-01-01T00:00:00Z',
        ]]);
    })->throws(InvalidArgumentException::class, '`attributes.threshold` must be a number');

test('::from() throws when minutes is the wrong type',
    function (): void {
        Monitor::from(['id' => '1', 'attributes' => [
            'type' => 'cpu_load', 'operator' => 'gte', 'threshold' => 90,
            'minutes' => '5',
            'notify' => 'a@b.c', 'status' => 'installed', 'state' => 'OK',
            'created_at' => '2026-01-01T00:00:00Z', 'updated_at' => '2026-01-01T00:00:00Z',
        ]]);
    })->throws(InvalidArgumentException::class, '`attributes.minutes` must be an integer or null');

test('::from() throws when a required date is missing',
    function (): void {
        Monitor::from(['id' => '1', 'attributes' => [
            'type' => 'cpu_load', 'operator' => 'gte', 'threshold' => 90,
            'notify' => 'a@b.c', 'status' => 'installed', 'state' => 'OK',
        ]]);
    })->throws(InvalidArgumentException::class, '`attributes.created_at` must be a string');

test('::from() throws when a required date is malformed',
    function (): void {
        Monitor::from(['id' => '1', 'attributes' => [
            'type' => 'cpu_load', 'operator' => 'gte', 'threshold' => 90,
            'notify' => 'a@b.c', 'status' => 'installed', 'state' => 'OK',
            'created_at' => 'not-a-date', 'updated_at' => '2026-01-01T00:00:00Z',
        ]]);
    })->throws(InvalidArgumentException::class, '`attributes.created_at` is not a valid date-time');

test('::from() throws when state_changed_at is malformed',
    function (): void {
        Monitor::from(['id' => '1', 'attributes' => [
            'type' => 'cpu_load', 'operator' => 'gte', 'threshold' => 90,
            'notify' => 'a@b.c', 'status' => 'installed', 'state' => 'OK',
            'state_changed_at' => 'not-a-date',
            'created_at' => '2026-01-01T00:00:00Z', 'updated_at' => '2026-01-01T00:00:00Z',
        ]]);
    })->throws(InvalidArgumentException::class, '`attributes.state_changed_at` is not a valid date-time');

test('jsonSerialize() emits snake_case keys with ATOM-formatted dates',
    function (): void {
        $monitor = $this->factory->make([
            'id' => '99',
            'threshold' => 75.0,
            'stateChangedAt' => new DateTimeImmutable('2026-03-01T00:00:00+00:00'),
            'createdAt' => new DateTimeImmutable('2026-01-01T00:00:00+00:00'),
            'updatedAt' => new DateTimeImmutable('2026-02-01T00:00:00+00:00'),
        ]);

        $json = $monitor->jsonSerialize();

        expect($json['id'])->toBe('99')
            ->and($json['threshold'])->toBe(75.0)
            ->and($json)->toHaveKey('minutes')
            ->and($json['state_changed_at'])->toBe('2026-03-01T00:00:00+00:00')
            ->and($json['created_at'])->toBe('2026-01-01T00:00:00+00:00')
            ->and($json['updated_at'])->toBe('2026-02-01T00:00:00+00:00');
    });

test('jsonSerialize() emits null state_changed_at when absent',
    function (): void {
        $monitor = $this->factory->make(['stateChangedAt' => null]);

        expect($monitor->jsonSerialize()['state_changed_at'])->toBeNull();
    });
