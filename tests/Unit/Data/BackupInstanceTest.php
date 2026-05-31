<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use DateTimeImmutable;
use InvalidArgumentException;
use PhpDevKits\ForgeSdk\Data\BackupInstance;
use Tests\Factories\BackupInstanceFactory;

beforeEach(function (): void {
    $this->factory = new BackupInstanceFactory;

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    $this->attrs = static fn (array $overrides = []): array => array_merge([
        'status' => 'finished',
        'is_partial' => false,
        'size' => 4096,
        'finished_at' => '2026-01-01T12:34:56Z',
    ], $overrides);
});

test('factory produces a usable BackupInstance',
    function (): void {
        expect($this->factory->make())->toBeInstanceOf(BackupInstance::class);
    });

test('::from() hydrates a full backup instance',
    function (): void {
        $instance = BackupInstance::from(['id' => '17', 'type' => 'backups', 'attributes' => ($this->attrs)()]);

        expect($instance->id)->toBe('17')
            ->and($instance->status)->toBe('finished')
            ->and($instance->isPartial)->toBeFalse()
            ->and($instance->size)->toBe(4096)
            ->and($instance->finishedAt->format('Y-m-d'))->toBe('2026-01-01');
    });

test('::from() throws when id is missing',
    function (): void {
        BackupInstance::from(['attributes' => []]);
    })->throws(InvalidArgumentException::class, 'missing the `id` field');

test('::from() throws when attributes is missing',
    function (): void {
        BackupInstance::from(['id' => '1']);
    })->throws(InvalidArgumentException::class, 'missing the `attributes` object');

test('::from() throws when size is not an integer',
    function (): void {
        BackupInstance::from(['id' => '1', 'attributes' => ($this->attrs)(['size' => '4096'])]);
    })->throws(InvalidArgumentException::class, '`attributes.size` must be an integer');

test('::from() throws when status is missing',
    function (): void {
        $attrs = ($this->attrs)();
        unset($attrs['status']);
        BackupInstance::from(['id' => '1', 'attributes' => $attrs]);
    })->throws(InvalidArgumentException::class, '`attributes.status` must be a string');

test('::from() throws when is_partial is not a bool',
    function (): void {
        BackupInstance::from(['id' => '1', 'attributes' => ($this->attrs)(['is_partial' => 'no'])]);
    })->throws(InvalidArgumentException::class, '`attributes.is_partial` must be a boolean');

test('::from() throws when finished_at is missing',
    function (): void {
        $attrs = ($this->attrs)();
        unset($attrs['finished_at']);
        BackupInstance::from(['id' => '1', 'attributes' => $attrs]);
    })->throws(InvalidArgumentException::class, '`attributes.finished_at` must be a string');

test('::from() throws when finished_at is malformed',
    function (): void {
        BackupInstance::from(['id' => '1', 'attributes' => ($this->attrs)(['finished_at' => 'not-a-date'])]);
    })->throws(InvalidArgumentException::class, '`attributes.finished_at` is not a valid date-time');

test('jsonSerialize() emits snake_case with an ATOM date',
    function (): void {
        $instance = $this->factory->make([
            'id' => '21',
            'status' => 'finished',
            'isPartial' => true,
            'size' => 9001,
            'finishedAt' => new DateTimeImmutable('2026-01-01T00:00:00+00:00'),
        ]);

        $json = $instance->jsonSerialize();

        expect($json['id'])->toBe('21')
            ->and($json['status'])->toBe('finished')
            ->and($json['is_partial'])->toBeTrue()
            ->and($json['size'])->toBe(9001)
            ->and($json['finished_at'])->toBe('2026-01-01T00:00:00+00:00');
    });
