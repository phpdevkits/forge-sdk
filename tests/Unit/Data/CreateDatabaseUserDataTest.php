<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use PhpDevKits\ForgeSdk\Data\CreateDatabaseUserData;
use Tests\Factories\CreateDatabaseUserDataFactory;

beforeEach(function (): void {
    $this->factory = new CreateDatabaseUserDataFactory;
});

test('factory produces a usable CreateDatabaseUserData',
    function (): void {
        $data = $this->factory->make();

        expect($data)->toBeInstanceOf(CreateDatabaseUserData::class)
            ->and($data->name)->toBeString()->not->toBeEmpty()
            ->and($data->password)->toBeString()->not->toBeEmpty()
            ->and($data->readOnly)->toBeNull()
            ->and($data->databaseIds)->toBeNull();
    });

test('toArray() emits only name and password when optional fields are null',
    function (): void {
        $data = new CreateDatabaseUserData(name: 'james', password: 'secret');

        expect($data->toArray())->toBe([
            'name' => 'james',
            'password' => 'secret',
        ]);
    });

test('toArray() emits read_only when set',
    function (): void {
        $data = new CreateDatabaseUserData(name: 'james', password: 'secret', readOnly: true);

        expect($data->toArray())->toBe([
            'name' => 'james',
            'password' => 'secret',
            'read_only' => true,
        ]);
    });

test('toArray() emits database_ids as a list of ints when set',
    function (): void {
        $data = new CreateDatabaseUserData(name: 'james', password: 'secret', databaseIds: [1, 2, 3]);

        expect($data->toArray())->toBe([
            'name' => 'james',
            'password' => 'secret',
            'database_ids' => [1, 2, 3],
        ]);
    });

test('toArray() emits all set fields together',
    function (): void {
        $data = new CreateDatabaseUserData(
            name: 'james',
            password: 'secret',
            readOnly: false,
            databaseIds: [7, 8],
        );

        expect($data->toArray())->toBe([
            'name' => 'james',
            'password' => 'secret',
            'read_only' => false,
            'database_ids' => [7, 8],
        ]);
    });

test('toArray() preserves empty database_ids array as an explicit grant reset',
    function (): void {
        $data = new CreateDatabaseUserData(name: 'james', password: 'secret', databaseIds: []);

        expect($data->toArray())->toBe([
            'name' => 'james',
            'password' => 'secret',
            'database_ids' => [],
        ]);
    });
