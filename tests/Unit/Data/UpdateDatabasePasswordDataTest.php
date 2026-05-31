<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use PhpDevKits\ForgeSdk\Data\UpdateDatabasePasswordData;
use Tests\Factories\UpdateDatabasePasswordDataFactory;

beforeEach(function (): void {
    $this->factory = new UpdateDatabasePasswordDataFactory;
});

test('factory produces a usable UpdateDatabasePasswordData',
    function (): void {
        expect($this->factory->make())->toBeInstanceOf(UpdateDatabasePasswordData::class);
    });

test('toArray() emits the password under the password key',
    function (): void {
        expect(new UpdateDatabasePasswordData(password: 'placeholder-value')->toArray())->toBe([
            'password' => 'placeholder-value',
        ]);
    });

test('toArray() preserves the password verbatim, including special chars',
    function (): void {
        expect(new UpdateDatabasePasswordData(password: 'a b!@#$%^&*()_+="\\/')->toArray())->toBe([
            'password' => 'a b!@#$%^&*()_+="\\/',
        ]);
    });
