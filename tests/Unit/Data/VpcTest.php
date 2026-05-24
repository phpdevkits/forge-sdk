<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use InvalidArgumentException;
use PhpDevKits\ForgeSdk\Data\Vpc;
use PhpDevKits\ForgeSdk\Data\VpcSubnet;
use Tests\Factories\VpcFactory;

beforeEach(function (): void {
    $this->factory = new VpcFactory;
});

test('factory produces a usable Vpc',
    function (): void {
        $vpc = $this->factory->make();

        expect($vpc)->toBeInstanceOf(Vpc::class)
            ->and($vpc->id)->toBeString()->not->toBeEmpty()
            ->and($vpc->name)->toBeString()->not->toBeEmpty()
            ->and($vpc->cidrBlock)->toBeString()->not->toBeEmpty()
            ->and($vpc->region)->toBeString()->not->toBeEmpty()
            ->and($vpc->subnets)->toBeArray();
    });

test('::from() hydrates from a JSON:API resource object including nested subnets',
    function (): void {
        $vpc = Vpc::from([
            'id' => 'vpc-123',
            'type' => 'vpcs',
            'attributes' => [
                'name' => 'my-vpc',
                'cidrBlock' => '10.0.0.0/16',
                'subnets' => [
                    ['id' => 'subnet-a', 'name' => 'a', 'cidrBlock' => '10.0.1.0/24'],
                    ['id' => 'subnet-b', 'name' => 'b', 'cidrBlock' => '10.0.2.0/24'],
                ],
                'region' => 'us-east-1',
            ],
            'links' => ['self' => ['href' => 'https://forge.laravel.com/api/.../vpcs/vpc-123']],
        ]);

        expect($vpc->id)->toBe('vpc-123')
            ->and($vpc->name)->toBe('my-vpc')
            ->and($vpc->cidrBlock)->toBe('10.0.0.0/16')
            ->and($vpc->region)->toBe('us-east-1')
            ->and($vpc->subnets)->toHaveCount(2)
            ->and($vpc->subnets[0])->toBeInstanceOf(VpcSubnet::class)
            ->and($vpc->subnets[0]->id)->toBe('subnet-a')
            ->and($vpc->subnets[1]->cidrBlock)->toBe('10.0.2.0/24');
    });

test('::from() hydrates an empty subnets array',
    function (): void {
        $vpc = Vpc::from([
            'id' => 'vpc-123',
            'attributes' => [
                'name' => 'my-vpc',
                'cidrBlock' => '10.0.0.0/16',
                'subnets' => [],
                'region' => 'us-east-1',
            ],
        ]);

        expect($vpc->subnets)->toBe([]);
    });

test('::from() throws when id is missing',
    function (): void {
        Vpc::from(['attributes' => ['name' => 'x', 'cidrBlock' => '10.0.0.0/16', 'subnets' => [], 'region' => 'r']]);
    })->throws(InvalidArgumentException::class, 'missing the `id` field');

test('::from() throws when attributes is missing',
    function (): void {
        Vpc::from(['id' => 'vpc-123']);
    })->throws(InvalidArgumentException::class, 'missing the `attributes` object');

test('::from() throws when a required string field is not a string',
    function (): void {
        Vpc::from(['id' => 'vpc-123', 'attributes' => ['name' => 123, 'cidrBlock' => '10.0.0.0/16', 'subnets' => [], 'region' => 'r']]);
    })->throws(InvalidArgumentException::class, '`attributes.name` must be a string');

test('::from() throws when subnets is not an array',
    function (): void {
        Vpc::from(['id' => 'vpc-123', 'attributes' => ['name' => 'x', 'cidrBlock' => '10.0.0.0/16', 'subnets' => 'nope', 'region' => 'r']]);
    })->throws(InvalidArgumentException::class, '`attributes.subnets` must be an array');

test('::from() throws when a subnet entry is not an object',
    function (): void {
        Vpc::from(['id' => 'vpc-123', 'attributes' => ['name' => 'x', 'cidrBlock' => '10.0.0.0/16', 'subnets' => ['nope'], 'region' => 'r']]);
    })->throws(InvalidArgumentException::class, '`attributes.subnets` must contain only objects');

test('jsonSerialize() emits the spec key shape with nested subnets',
    function (): void {
        $subnet = new VpcSubnet(id: 'subnet-a', name: 'a', cidrBlock: '10.0.1.0/24');
        $vpc = new Vpc(
            id: 'vpc-123',
            name: 'my-vpc',
            cidrBlock: '10.0.0.0/16',
            subnets: [$subnet],
            region: 'us-east-1',
        );

        expect($vpc->jsonSerialize())->toBe([
            'id' => 'vpc-123',
            'name' => 'my-vpc',
            'cidrBlock' => '10.0.0.0/16',
            'subnets' => [$subnet],
            'region' => 'us-east-1',
        ]);
    });

test('json_encode() serializes a Vpc and its nested subnets',
    function (): void {
        $vpc = new Vpc(
            id: 'vpc-123',
            name: 'my-vpc',
            cidrBlock: '10.0.0.0/16',
            subnets: [new VpcSubnet(id: 'subnet-a', name: 'a', cidrBlock: '10.0.1.0/24')],
            region: 'us-east-1',
        );

        expect(json_decode(json_encode($vpc, JSON_THROW_ON_ERROR), true))->toBe([
            'id' => 'vpc-123',
            'name' => 'my-vpc',
            'cidrBlock' => '10.0.0.0/16',
            'subnets' => [
                ['id' => 'subnet-a', 'name' => 'a', 'cidrBlock' => '10.0.1.0/24'],
            ],
            'region' => 'us-east-1',
        ]);
    });
