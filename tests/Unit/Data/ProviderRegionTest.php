<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use InvalidArgumentException;
use PhpDevKits\ForgeSdk\Data\ProviderRegion;
use Tests\Factories\ProviderRegionFactory;

beforeEach(function (): void {
    $this->factory = new ProviderRegionFactory;
});

test('factory produces a usable ProviderRegion',
    function (): void {
        $region = $this->factory->make();

        expect($region)->toBeInstanceOf(ProviderRegion::class)
            ->and($region->id)->toBeString()->not->toBeEmpty()
            ->and($region->code)->toBeString()->not->toBeEmpty();
    });

test('::from() hydrates from a JSON:API resource object',
    function (): void {
        $region = ProviderRegion::from([
            'id' => '1',
            'type' => 'providerRegions',
            'attributes' => ['name' => 'New York 3', 'code' => 'nyc3', 'alternate_code' => 'us-east'],
        ]);

        expect($region->name)->toBe('New York 3')
            ->and($region->code)->toBe('nyc3')
            ->and($region->alternateCode)->toBe('us-east');
    });

test('::from() leaves alternate_code null when the spec sends null',
    function (): void {
        $region = ProviderRegion::from([
            'id' => '1',
            'attributes' => ['name' => 'X', 'code' => 'x', 'alternate_code' => null],
        ]);

        expect($region->alternateCode)->toBeNull();
    });

test('::from() throws when id is missing',
    function (): void {
        ProviderRegion::from(['attributes' => ['name' => 'X', 'code' => 'x']]);
    })->throws(InvalidArgumentException::class, 'missing the `id` field');

test('::from() throws when attributes is missing',
    function (): void {
        ProviderRegion::from(['id' => '1']);
    })->throws(InvalidArgumentException::class, 'missing the `attributes` object');

test('::from() throws when name is not a string',
    function (): void {
        ProviderRegion::from(['id' => '1', 'attributes' => ['name' => 123, 'code' => 'x']]);
    })->throws(InvalidArgumentException::class, '`attributes.name` must be a string');

test('::from() throws when code is not a string',
    function (): void {
        ProviderRegion::from(['id' => '1', 'attributes' => ['name' => 'X']]);
    })->throws(InvalidArgumentException::class, '`attributes.code` must be a string');

test('::from() throws when alternate_code is the wrong type',
    function (): void {
        ProviderRegion::from(['id' => '1', 'attributes' => ['name' => 'X', 'code' => 'x', 'alternate_code' => 42]]);
    })->throws(InvalidArgumentException::class, '`attributes.alternate_code` must be a string or null');

test('jsonSerialize() emits snake_case keys',
    function (): void {
        $region = new ProviderRegion(id: '1', name: 'X', code: 'x', alternateCode: 'alt');

        expect($region->jsonSerialize())->toBe([
            'id' => '1',
            'name' => 'X',
            'code' => 'x',
            'alternate_code' => 'alt',
        ]);
    });
