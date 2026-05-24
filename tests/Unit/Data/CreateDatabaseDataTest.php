<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use PhpDevKits\ForgeSdk\Data\CreateDatabaseData;
use Tests\Factories\CreateDatabaseDataFactory;

beforeEach(function (): void {
    $this->factory = new CreateDatabaseDataFactory;
});

test('factory produces a usable CreateDatabaseData',
    function (): void {
        $data = $this->factory->make();

        expect($data)->toBeInstanceOf(CreateDatabaseData::class)
            ->and($data->name)->toBeString()->not->toBeEmpty()
            ->and($data->user)->toBeNull()
            ->and($data->password)->toBeNull();
    });

test('toArray() emits only the name when user and password are null',
    function (): void {
        $data = new CreateDatabaseData(name: 'forge');

        expect($data->toArray())->toBe([
            'name' => 'forge',
        ]);
    });

test('toArray() emits user when set',
    function (): void {
        $data = new CreateDatabaseData(name: 'forge', user: 'james');

        expect($data->toArray())->toBe([
            'name' => 'forge',
            'user' => 'james',
        ]);
    });

test('toArray() emits user and password when both set',
    function (): void {
        $data = new CreateDatabaseData(name: 'forge', user: 'james', password: 'secret');

        expect($data->toArray())->toBe([
            'name' => 'forge',
            'user' => 'james',
            'password' => 'secret',
        ]);
    });
