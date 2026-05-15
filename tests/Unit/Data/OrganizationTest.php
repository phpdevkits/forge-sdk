<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use DateTimeImmutable;
use InvalidArgumentException;
use PhpDevKits\ForgeSdk\Data\Organization;

test('::from() hydrates from a JSON:API resource object',
    function (): void {
        $organization = Organization::from([
            'id' => '42',
            'type' => 'organizations',
            'attributes' => [
                'name' => 'Acme Inc.',
                'slug' => 'acme',
                'created_at' => '2024-05-14T12:00:00Z',
                'updated_at' => '2024-05-15T08:30:00Z',
            ],
            'links' => ['self' => ['href' => 'https://forge.laravel.com/api/orgs/acme']],
        ]);

        expect($organization->id)->toBe('42')
            ->and($organization->name)->toBe('Acme Inc.')
            ->and($organization->slug)->toBe('acme')
            ->and($organization->createdAt)->toBeInstanceOf(DateTimeImmutable::class)
            ->and($organization->createdAt?->format('c'))->toBe('2024-05-14T12:00:00+00:00')
            ->and($organization->updatedAt)->toBeInstanceOf(DateTimeImmutable::class);
    });

test('::from() leaves created_at and updated_at null when the spec sends null',
    function (): void {
        $organization = Organization::from([
            'id' => '1',
            'attributes' => [
                'name' => 'Bare',
                'slug' => 'bare',
                'created_at' => null,
                'updated_at' => null,
            ],
        ]);

        expect($organization->createdAt)->toBeNull()
            ->and($organization->updatedAt)->toBeNull();
    });

test('::from() throws when id is missing',
    function (): void {
        Organization::from(['attributes' => ['name' => 'X', 'slug' => 'x']]);
    })->throws(InvalidArgumentException::class, 'missing the `id` field');

test('::from() throws when attributes is missing',
    function (): void {
        Organization::from(['id' => '1']);
    })->throws(InvalidArgumentException::class, 'missing the `attributes` object');

test('::from() throws when attributes.name is not a string',
    function (): void {
        Organization::from(['id' => '1', 'attributes' => ['slug' => 'x', 'name' => 123]]);
    })->throws(InvalidArgumentException::class, '`attributes.name` must be a string');

test('::from() throws when attributes.slug is not a string',
    function (): void {
        Organization::from(['id' => '1', 'attributes' => ['name' => 'X']]);
    })->throws(InvalidArgumentException::class, '`attributes.slug` must be a string');

test('::from() throws when created_at is the wrong type',
    function (): void {
        Organization::from([
            'id' => '1',
            'attributes' => ['name' => 'X', 'slug' => 'x', 'created_at' => 12345],
        ]);
    })->throws(InvalidArgumentException::class, '`attributes.created_at` must be a string or null');

test('::from() throws when created_at is an unparseable string',
    function (): void {
        Organization::from([
            'id' => '1',
            'attributes' => ['name' => 'X', 'slug' => 'x', 'created_at' => 'not-a-date'],
        ]);
    })->throws(InvalidArgumentException::class, '`attributes.created_at` is not a valid date-time');

test('jsonSerialize() emits the JSON:API attribute names',
    function (): void {
        $organization = new Organization(
            id: '1',
            name: 'Acme',
            slug: 'acme',
            createdAt: new DateTimeImmutable('2024-05-14T12:00:00Z'),
            updatedAt: null,
        );

        expect($organization->jsonSerialize())->toBe([
            'id' => '1',
            'name' => 'Acme',
            'slug' => 'acme',
            'created_at' => '2024-05-14T12:00:00+00:00',
            'updated_at' => null,
        ]);
    });
