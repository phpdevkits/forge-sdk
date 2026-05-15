<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use PhpDevKits\ForgeSdk\Data\HetznerServerConfig;
use Tests\Factories\HetznerServerConfigFactory;

beforeEach(function (): void {
    $this->factory = new HetznerServerConfigFactory;
});

test('factory produces a usable HetznerServerConfig',
    function (): void {
        $config = $this->factory->make();

        expect($config)->toBeInstanceOf(HetznerServerConfig::class)
            ->and($config->regionId)->toBeString()->not->toBeEmpty()
            ->and($config->sizeId)->toBeString()->not->toBeEmpty();
    });

test('toArray() includes only the required fields when optionals are null',
    function (): void {
        $config = new HetznerServerConfig(regionId: 'fsn1', sizeId: 'cpx11');

        expect($config->toArray())->toBe([
            'region_id' => 'fsn1',
            'size_id' => 'cpx11',
        ]);
    });

test('toArray() emits network_id when set',
    function (): void {
        $config = new HetznerServerConfig(regionId: 'fsn1', sizeId: 'cpx11', networkId: 42);

        expect($config->toArray())->toBe([
            'region_id' => 'fsn1',
            'size_id' => 'cpx11',
            'network_id' => 42,
        ]);
    });

test('toArray() emits enable_daily_backups when set',
    function (): void {
        $config = new HetznerServerConfig(regionId: 'fsn1', sizeId: 'cpx11', enableDailyBackups: true);

        expect($config->toArray())->toBe([
            'region_id' => 'fsn1',
            'size_id' => 'cpx11',
            'enable_daily_backups' => true,
        ]);
    });
