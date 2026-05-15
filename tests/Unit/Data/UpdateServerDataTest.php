<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use PhpDevKits\ForgeSdk\Data\UpdateServerData;
use Tests\Factories\UpdateServerDataFactory;

beforeEach(function (): void {
    $this->factory = new UpdateServerDataFactory;
});

test('factory produces a usable UpdateServerData',
    function (): void {
        $data = $this->factory->make();

        expect($data)->toBeInstanceOf(UpdateServerData::class);
    });

test('toArray() is empty when nothing is set',
    function (): void {
        expect((new UpdateServerData)->toArray())->toBe([]);
    });

test('toArray() emits name when set',
    function (): void {
        expect(new UpdateServerData(name: 'renamed')->toArray())->toBe(['name' => 'renamed']);
    });

test('toArray() emits ip_address when set',
    function (): void {
        expect(new UpdateServerData(ipAddress: '10.0.0.5')->toArray())->toBe(['ip_address' => '10.0.0.5']);
    });

test('toArray() emits private_ip_address when set',
    function (): void {
        expect(new UpdateServerData(privateIpAddress: '10.0.0.6')->toArray())->toBe(['private_ip_address' => '10.0.0.6']);
    });

test('toArray() emits timezone when set',
    function (): void {
        expect(new UpdateServerData(timezone: 'Europe/Lisbon')->toArray())->toBe(['timezone' => 'Europe/Lisbon']);
    });

test('toArray() emits tags when set',
    function (): void {
        expect(new UpdateServerData(tags: ['production', 'web'])->toArray())->toBe(['tags' => ['production', 'web']]);
    });
