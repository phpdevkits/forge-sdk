<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use DateTimeImmutable;
use InvalidArgumentException;
use PhpDevKits\ForgeSdk\Data\ServerCredential;
use Tests\Factories\ServerCredentialFactory;

beforeEach(function (): void {
    $this->factory = new ServerCredentialFactory;
});

test('factory produces a usable ServerCredential',
    function (): void {
        $credential = $this->factory->make();

        expect($credential)->toBeInstanceOf(ServerCredential::class)
            ->and($credential->id)->toBeString()->not->toBeEmpty()
            ->and($credential->name)->toBeString()->not->toBeEmpty()
            ->and($credential->provider)->toBeString()->not->toBeEmpty()
            ->and($credential->inUse)->toBeBool();
    });

test('::from() hydrates from a JSON:API resource object',
    function (): void {
        $credential = ServerCredential::from([
            'id' => '7',
            'type' => 'serverCredentials',
            'attributes' => [
                'name' => 'AWS Production',
                'provider' => 'aws',
                'in_use' => true,
                'created_at' => '2026-01-02T03:04:05+00:00',
                'updated_at' => '2026-01-03T03:04:05+00:00',
            ],
            'links' => ['self' => ['href' => 'https://forge.laravel.com/api/orgs/acme/server-credentials/7']],
        ]);

        expect($credential->id)->toBe('7')
            ->and($credential->name)->toBe('AWS Production')
            ->and($credential->provider)->toBe('aws')
            ->and($credential->inUse)->toBeTrue()
            ->and($credential->createdAt)->toBeInstanceOf(DateTimeImmutable::class)
            ->and($credential->updatedAt)->toBeInstanceOf(DateTimeImmutable::class);
    });

test('::from() leaves nullable timestamps null when the spec sends null',
    function (): void {
        $credential = ServerCredential::from([
            'id' => '1',
            'attributes' => [
                'name' => 'X',
                'provider' => 'hetzner',
                'in_use' => false,
                'created_at' => null,
                'updated_at' => null,
            ],
        ]);

        expect($credential->createdAt)->toBeNull()
            ->and($credential->updatedAt)->toBeNull();
    });

test('::from() throws when id is missing',
    function (): void {
        ServerCredential::from(['attributes' => ['name' => 'X', 'provider' => 'aws', 'in_use' => false]]);
    })->throws(InvalidArgumentException::class, 'missing the `id` field');

test('::from() throws when attributes is missing',
    function (): void {
        ServerCredential::from(['id' => '1']);
    })->throws(InvalidArgumentException::class, 'missing the `attributes` object');

test('::from() throws when a required string field is not a string',
    function (): void {
        ServerCredential::from(['id' => '1', 'attributes' => ['name' => 123, 'provider' => 'aws', 'in_use' => false]]);
    })->throws(InvalidArgumentException::class, '`attributes.name` must be a string');

test('::from() throws when in_use is not a boolean',
    function (): void {
        ServerCredential::from(['id' => '1', 'attributes' => ['name' => 'X', 'provider' => 'aws', 'in_use' => 'yes']]);
    })->throws(InvalidArgumentException::class, '`attributes.in_use` must be a boolean');

test('::from() throws when a timestamp is the wrong type',
    function (): void {
        ServerCredential::from(['id' => '1', 'attributes' => [
            'name' => 'X', 'provider' => 'aws', 'in_use' => false, 'created_at' => 123,
        ]]);
    })->throws(InvalidArgumentException::class, '`attributes.created_at` must be a string or null');

test('::from() throws when a timestamp is not a valid date-time',
    function (): void {
        ServerCredential::from(['id' => '1', 'attributes' => [
            'name' => 'X', 'provider' => 'aws', 'in_use' => false, 'created_at' => 'not-a-date',
        ]]);
    })->throws(InvalidArgumentException::class, '`attributes.created_at` is not a valid date-time');

test('jsonSerialize() emits snake_case keys matching the JSON:API attribute names',
    function (): void {
        $credential = new ServerCredential(
            id: '7',
            name: 'AWS Production',
            provider: 'aws',
            inUse: true,
            createdAt: new DateTimeImmutable('2026-01-02T03:04:05+00:00'),
            updatedAt: new DateTimeImmutable('2026-01-03T03:04:05+00:00'),
        );

        expect($credential->jsonSerialize())->toBe([
            'id' => '7',
            'name' => 'AWS Production',
            'provider' => 'aws',
            'in_use' => true,
            'created_at' => '2026-01-02T03:04:05+00:00',
            'updated_at' => '2026-01-03T03:04:05+00:00',
        ]);
    });

test('jsonSerialize() emits null for absent timestamps',
    function (): void {
        $credential = new ServerCredential(
            id: '1',
            name: 'X',
            provider: 'hetzner',
            inUse: false,
            createdAt: null,
            updatedAt: null,
        );

        expect($credential->jsonSerialize())->toBe([
            'id' => '1',
            'name' => 'X',
            'provider' => 'hetzner',
            'in_use' => false,
            'created_at' => null,
            'updated_at' => null,
        ]);
    });
