<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use InvalidArgumentException;
use PhpDevKits\ForgeSdk\Data\ProviderSize;
use Tests\Factories\ProviderSizeFactory;

beforeEach(function (): void {
    $this->factory = new ProviderSizeFactory;
});

test('factory produces a usable ProviderSize',
    function (): void {
        $size = $this->factory->make();

        expect($size)->toBeInstanceOf(ProviderSize::class)
            ->and($size->cpus)->toBeInt()->toBeGreaterThan(0)
            ->and($size->ram)->toBeInt()->toBeGreaterThan(0)
            ->and($size->disk)->toBeInt()->toBeGreaterThan(0);
    });

test('::from() hydrates from a JSON:API resource object',
    function (): void {
        $size = ProviderSize::from([
            'id' => '7',
            'type' => 'providerSizes',
            'attributes' => [
                'name' => 'Basic 2 vCPU 4 GB',
                'code' => 's-2vcpu-4gb',
                'series' => 'general',
                'category' => 'standard',
                'cpus' => 2,
                'disk_type' => 'ssd',
                'architecture' => 'x86_64',
                'ram' => 4096,
                'disk' => 81920,
            ],
        ]);

        expect($size->id)->toBe('7')
            ->and($size->name)->toBe('Basic 2 vCPU 4 GB')
            ->and($size->code)->toBe('s-2vcpu-4gb')
            ->and($size->series)->toBe('general')
            ->and($size->category)->toBe('standard')
            ->and($size->cpus)->toBe(2)
            ->and($size->diskType)->toBe('ssd')
            ->and($size->architecture)->toBe('x86_64')
            ->and($size->ram)->toBe(4096)
            ->and($size->disk)->toBe(81920);
    });

test('::from() throws when id is missing',
    function (): void {
        ProviderSize::from(['attributes' => []]);
    })->throws(InvalidArgumentException::class, 'missing the `id` field');

test('::from() throws when attributes is missing',
    function (): void {
        ProviderSize::from(['id' => '1']);
    })->throws(InvalidArgumentException::class, 'missing the `attributes` object');

test('::from() throws when a required string field is missing',
    function (): void {
        ProviderSize::from(['id' => '1', 'attributes' => [
            'code' => 'x', 'series' => 'g', 'category' => 's',
            'cpus' => 1, 'disk_type' => 'ssd', 'architecture' => 'x',
            'ram' => 1, 'disk' => 1,
        ]]);
    })->throws(InvalidArgumentException::class, '`attributes.name` must be a string');

test('::from() hydrates a sparse object (region-scoped sizes endpoint)',
    function (): void {
        $size = ProviderSize::from([
            'id' => '7',
            'attributes' => ['name' => 'Sparse Size'],
        ]);

        expect($size->id)->toBe('7')
            ->and($size->name)->toBe('Sparse Size')
            ->and($size->code)->toBeNull()
            ->and($size->cpus)->toBeNull()
            ->and($size->architecture)->toBeNull();
    });

test('::from() throws when an optional integer field is the wrong type',
    function (): void {
        ProviderSize::from(['id' => '1', 'attributes' => ['name' => 'X', 'cpus' => '2']]);
    })->throws(InvalidArgumentException::class, '`attributes.cpus` must be an integer or null');

test('::from() throws when an optional string field is the wrong type',
    function (): void {
        ProviderSize::from(['id' => '1', 'attributes' => ['name' => 'X', 'code' => 42]]);
    })->throws(InvalidArgumentException::class, '`attributes.code` must be a string or null');

test('jsonSerialize() emits snake_case keys',
    function (): void {
        $size = new ProviderSize(
            id: '1', name: 'X', code: 'x', series: 'g', category: 's',
            cpus: 2, diskType: 'ssd', architecture: 'x86_64',
            ram: 4096, disk: 81920,
        );

        expect($size->jsonSerialize())->toBe([
            'id' => '1', 'name' => 'X', 'code' => 'x', 'series' => 'g', 'category' => 's',
            'cpus' => 2, 'disk_type' => 'ssd', 'architecture' => 'x86_64',
            'ram' => 4096, 'disk' => 81920,
        ]);
    });
