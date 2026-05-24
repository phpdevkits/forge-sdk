<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use InvalidArgumentException;
use PhpDevKits\ForgeSdk\Data\VpcSubnet;
use Tests\Factories\VpcSubnetFactory;

beforeEach(function (): void {
    $this->factory = new VpcSubnetFactory;
});

test('factory produces a usable VpcSubnet',
    function (): void {
        $subnet = $this->factory->make();

        expect($subnet)->toBeInstanceOf(VpcSubnet::class)
            ->and($subnet->id)->toBeString()->not->toBeEmpty()
            ->and($subnet->name)->toBeString()->not->toBeEmpty()
            ->and($subnet->cidrBlock)->toBeString()->not->toBeEmpty();
    });

test('::from() hydrates from a subnet object',
    function (): void {
        $subnet = VpcSubnet::from([
            'id' => 'subnet-123',
            'name' => 'subnet-a',
            'cidrBlock' => '10.0.1.0/24',
        ]);

        expect($subnet->id)->toBe('subnet-123')
            ->and($subnet->name)->toBe('subnet-a')
            ->and($subnet->cidrBlock)->toBe('10.0.1.0/24');
    });

test('::from() throws when a required field is not a string',
    function (): void {
        VpcSubnet::from(['id' => 'subnet-123', 'name' => 'subnet-a', 'cidrBlock' => 123]);
    })->throws(InvalidArgumentException::class, 'VpcSubnet `cidrBlock` must be a string');

test('jsonSerialize() emits the camelCase keys the spec uses',
    function (): void {
        $subnet = new VpcSubnet(id: 'subnet-123', name: 'subnet-a', cidrBlock: '10.0.1.0/24');

        expect($subnet->jsonSerialize())->toBe([
            'id' => 'subnet-123',
            'name' => 'subnet-a',
            'cidrBlock' => '10.0.1.0/24',
        ]);
    });
