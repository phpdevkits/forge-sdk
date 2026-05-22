<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use DateTimeImmutable;
use InvalidArgumentException;
use PhpDevKits\ForgeSdk\Data\SshKey;
use Tests\Factories\SshKeyFactory;

beforeEach(function (): void {
    $this->factory = new SshKeyFactory;
});

test('factory produces a usable SshKey',
    function (): void {
        $key = $this->factory->make();

        expect($key)->toBeInstanceOf(SshKey::class)
            ->and($key->id)->toBeString()->not->toBeEmpty()
            ->and($key->createdAt)->toBeInstanceOf(DateTimeImmutable::class);
    });

test('::from() hydrates a full JSON:API resource object',
    function (): void {
        $key = SshKey::from([
            'id' => '42',
            'type' => 'keys',
            'attributes' => [
                'name' => 'oliver-macbook',
                'user' => 'forge',
                'status' => 'installed',
                'created_by' => 7,
                'created_at' => '2026-04-01T00:00:00Z',
                'updated_at' => '2026-05-01T00:00:00Z',
            ],
        ]);

        expect($key->id)->toBe('42')
            ->and($key->name)->toBe('oliver-macbook')
            ->and($key->user)->toBe('forge')
            ->and($key->status)->toBe('installed')
            ->and($key->createdBy)->toBe(7)
            ->and($key->createdAt->format('Y-m-d'))->toBe('2026-04-01');
    });

test('::from() accepts null created_by',
    function (): void {
        $key = SshKey::from([
            'id' => '1',
            'attributes' => [
                'name' => 'x',
                'user' => 'forge',
                'status' => 'installed',
                'created_by' => null,
                'created_at' => '2026-01-01T00:00:00Z',
                'updated_at' => '2026-01-01T00:00:00Z',
            ],
        ]);

        expect($key->createdBy)->toBeNull();
    });

test('::from() throws when id is missing',
    function (): void {
        SshKey::from(['attributes' => []]);
    })->throws(InvalidArgumentException::class, 'missing the `id` field');

test('::from() throws when attributes is missing',
    function (): void {
        SshKey::from(['id' => '1']);
    })->throws(InvalidArgumentException::class, 'missing the `attributes` object');

test('::from() throws when a required string field is missing',
    function (): void {
        SshKey::from(['id' => '1', 'attributes' => []]);
    })->throws(InvalidArgumentException::class, '`attributes.name` must be a string');

test('::from() throws when created_by is the wrong type',
    function (): void {
        SshKey::from(['id' => '1', 'attributes' => [
            'name' => 'x', 'user' => 'forge', 'status' => 'installed',
            'created_by' => '7',
            'created_at' => '2026-01-01T00:00:00Z',
            'updated_at' => '2026-01-01T00:00:00Z',
        ]]);
    })->throws(InvalidArgumentException::class, '`attributes.created_by` must be an integer or null');

test('::from() throws when a required date is missing',
    function (): void {
        SshKey::from(['id' => '1', 'attributes' => [
            'name' => 'x', 'user' => 'forge', 'status' => 'installed',
        ]]);
    })->throws(InvalidArgumentException::class, '`attributes.created_at` must be a string');

test('::from() throws when a required date is malformed',
    function (): void {
        SshKey::from(['id' => '1', 'attributes' => [
            'name' => 'x', 'user' => 'forge', 'status' => 'installed',
            'created_at' => 'not-a-date',
            'updated_at' => '2026-01-01T00:00:00Z',
        ]]);
    })->throws(InvalidArgumentException::class, '`attributes.created_at` is not a valid date-time');

test('jsonSerialize() emits snake_case keys with ATOM-formatted dates',
    function (): void {
        $key = $this->factory->make([
            'id' => '99',
            'createdAt' => new DateTimeImmutable('2026-01-01T00:00:00+00:00'),
            'updatedAt' => new DateTimeImmutable('2026-02-01T00:00:00+00:00'),
        ]);

        $json = $key->jsonSerialize();

        expect($json['id'])->toBe('99')
            ->and($json)->toHaveKey('created_by')
            ->and($json['created_at'])->toBe('2026-01-01T00:00:00+00:00')
            ->and($json['updated_at'])->toBe('2026-02-01T00:00:00+00:00');
    });
