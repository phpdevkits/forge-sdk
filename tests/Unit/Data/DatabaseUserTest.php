<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use DateTimeImmutable;
use InvalidArgumentException;
use PhpDevKits\ForgeSdk\Data\DatabaseUser;
use Tests\Factories\DatabaseUserFactory;

beforeEach(function (): void {
    $this->factory = new DatabaseUserFactory;
});

test('factory produces a usable DatabaseUser',
    function (): void {
        $user = $this->factory->make();

        expect($user)->toBeInstanceOf(DatabaseUser::class)
            ->and($user->id)->toBeString()->not->toBeEmpty()
            ->and($user->createdAt)->toBeInstanceOf(DateTimeImmutable::class);
    });

test('::from() hydrates a full JSON:API resource object',
    function (): void {
        $user = DatabaseUser::from([
            'id' => '42',
            'type' => 'databaseUsers',
            'attributes' => [
                'name' => 'forge',
                'status' => 'installed',
                'created_at' => '2026-04-01T00:00:00Z',
                'updated_at' => '2026-05-01T00:00:00Z',
            ],
        ]);

        expect($user->id)->toBe('42')
            ->and($user->name)->toBe('forge')
            ->and($user->status)->toBe('installed')
            ->and($user->createdAt->format('Y-m-d'))->toBe('2026-04-01')
            ->and($user->updatedAt->format('Y-m-d'))->toBe('2026-05-01');
    });

test('::from() throws when id is missing',
    function (): void {
        DatabaseUser::from(['attributes' => []]);
    })->throws(InvalidArgumentException::class, 'missing the `id` field');

test('::from() throws when attributes is missing',
    function (): void {
        DatabaseUser::from(['id' => '1']);
    })->throws(InvalidArgumentException::class, 'missing the `attributes` object');

test('::from() throws when a required string field is missing',
    function (): void {
        DatabaseUser::from(['id' => '1', 'attributes' => []]);
    })->throws(InvalidArgumentException::class, '`attributes.name` must be a string');

test('::from() throws when status is the wrong type',
    function (): void {
        DatabaseUser::from(['id' => '1', 'attributes' => [
            'name' => 'forge',
            'status' => 123,
        ]]);
    })->throws(InvalidArgumentException::class, '`attributes.status` must be a string');

test('::from() throws when a required date is missing',
    function (): void {
        DatabaseUser::from(['id' => '1', 'attributes' => [
            'name' => 'forge', 'status' => 'installed',
        ]]);
    })->throws(InvalidArgumentException::class, '`attributes.created_at` must be a string');

test('::from() throws when a required date is malformed',
    function (): void {
        DatabaseUser::from(['id' => '1', 'attributes' => [
            'name' => 'forge', 'status' => 'installed',
            'created_at' => 'not-a-date',
            'updated_at' => '2026-01-01T00:00:00Z',
        ]]);
    })->throws(InvalidArgumentException::class, '`attributes.created_at` is not a valid date-time');

test('jsonSerialize() emits snake_case keys with ATOM-formatted dates',
    function (): void {
        $user = $this->factory->make([
            'id' => '99',
            'name' => 'forge',
            'status' => 'installed',
            'createdAt' => new DateTimeImmutable('2026-01-01T00:00:00+00:00'),
            'updatedAt' => new DateTimeImmutable('2026-02-01T00:00:00+00:00'),
        ]);

        $json = $user->jsonSerialize();

        expect($json)->toBe([
            'id' => '99',
            'name' => 'forge',
            'status' => 'installed',
            'created_at' => '2026-01-01T00:00:00+00:00',
            'updated_at' => '2026-02-01T00:00:00+00:00',
        ]);
    });
