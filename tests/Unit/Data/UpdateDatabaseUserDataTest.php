<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use PhpDevKits\ForgeSdk\Data\UpdateDatabaseUserData;
use Tests\Factories\UpdateDatabaseUserDataFactory;

beforeEach(function (): void {
    $this->factory = new UpdateDatabaseUserDataFactory;
});

test('factory produces a usable UpdateDatabaseUserData',
    function (): void {
        expect($this->factory->make())->toBeInstanceOf(UpdateDatabaseUserData::class);
    });

test('toArray() emits an empty array when no fields are set',
    function (): void {
        expect((new UpdateDatabaseUserData)->toArray())->toBe([]);
    });

test('toArray() emits password when set',
    function (): void {
        expect(new UpdateDatabaseUserData(password: 'new-secret')->toArray())->toBe([
            'password' => 'new-secret',
        ]);
    });

test('toArray() emits database_ids when set',
    function (): void {
        expect(new UpdateDatabaseUserData(databaseIds: [1, 2])->toArray())->toBe([
            'database_ids' => [1, 2],
        ]);
    });

test('toArray() emits both fields together',
    function (): void {
        expect(new UpdateDatabaseUserData(password: 'new-secret', databaseIds: [3])->toArray())->toBe([
            'password' => 'new-secret',
            'database_ids' => [3],
        ]);
    });

test('toArray() preserves empty database_ids as an explicit grant revoke',
    function (): void {
        expect(new UpdateDatabaseUserData(databaseIds: [])->toArray())->toBe([
            'database_ids' => [],
        ]);
    });
