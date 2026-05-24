<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use PhpDevKits\ForgeSdk\Data\UpdateDaemonData;
use Tests\Factories\UpdateDaemonDataFactory;

beforeEach(function (): void {
    $this->factory = new UpdateDaemonDataFactory;
});

test('factory produces a usable UpdateDaemonData',
    function (): void {
        expect($this->factory->make())->toBeInstanceOf(UpdateDaemonData::class);
    });

test('toArray() emits only name when config is null',
    function (): void {
        expect(new UpdateDaemonData(name: 'worker')->toArray())->toBe(['name' => 'worker']);
    });

test('toArray() emits config when set',
    function (): void {
        expect(new UpdateDaemonData(name: 'worker', config: '[program:x]')->toArray())->toBe([
            'name' => 'worker',
            'config' => '[program:x]',
        ]);
    });
