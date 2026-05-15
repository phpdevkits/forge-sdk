<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use InvalidArgumentException;
use PhpDevKits\ForgeSdk\Data\Provider;
use Tests\Factories\ProviderFactory;

beforeEach(function (): void {
    $this->factory = new ProviderFactory;
});

test('factory produces a usable Provider',
    function (): void {
        $provider = $this->factory->make();

        expect($provider)->toBeInstanceOf(Provider::class)
            ->and($provider->id)->toBeString()->not->toBeEmpty()
            ->and($provider->slug)->toBeString()->not->toBeEmpty()
            ->and($provider->currency)->toBe('USD');
    });

test('::from() hydrates from a JSON:API resource object',
    function (): void {
        $provider = Provider::from([
            'id' => '1',
            'type' => 'providers',
            'attributes' => [
                'name' => 'DigitalOcean',
                'slug' => 'digitalocean',
                'simple_name' => 'DO',
                'currency' => 'USD',
                'currency_symbol' => '$',
                'default_size_code' => 's-1vcpu-1gb',
                'default_region_code' => 'nyc3',
            ],
            'links' => ['self' => ['href' => 'https://forge.laravel.com/api/providers/digitalocean']],
        ]);

        expect($provider->name)->toBe('DigitalOcean')
            ->and($provider->slug)->toBe('digitalocean')
            ->and($provider->simpleName)->toBe('DO')
            ->and($provider->currency)->toBe('USD')
            ->and($provider->currencySymbol)->toBe('$')
            ->and($provider->defaultSizeCode)->toBe('s-1vcpu-1gb')
            ->and($provider->defaultRegionCode)->toBe('nyc3');
    });

test('::from() leaves nullable fields null when the spec sends null',
    function (): void {
        $provider = Provider::from([
            'id' => '1',
            'attributes' => [
                'name' => 'X',
                'slug' => 'x',
                'simple_name' => null,
                'currency' => 'USD',
                'currency_symbol' => '$',
                'default_size_code' => null,
                'default_region_code' => null,
            ],
        ]);

        expect($provider->simpleName)->toBeNull()
            ->and($provider->defaultSizeCode)->toBeNull()
            ->and($provider->defaultRegionCode)->toBeNull();
    });

test('::from() throws when id is missing',
    function (): void {
        Provider::from(['attributes' => ['name' => 'X', 'slug' => 'x', 'currency' => 'USD', 'currency_symbol' => '$']]);
    })->throws(InvalidArgumentException::class, 'missing the `id` field');

test('::from() throws when attributes is missing',
    function (): void {
        Provider::from(['id' => '1']);
    })->throws(InvalidArgumentException::class, 'missing the `attributes` object');

test('::from() throws when a required string field is not a string',
    function (): void {
        Provider::from(['id' => '1', 'attributes' => ['name' => 123, 'slug' => 'x', 'currency' => 'USD', 'currency_symbol' => '$']]);
    })->throws(InvalidArgumentException::class, '`attributes.name` must be a string');

test('::from() throws when a nullable field is the wrong type',
    function (): void {
        Provider::from(['id' => '1', 'attributes' => [
            'name' => 'X', 'slug' => 'x', 'currency' => 'USD', 'currency_symbol' => '$',
            'simple_name' => 123,
        ]]);
    })->throws(InvalidArgumentException::class, '`attributes.simple_name` must be a string or null');

test('jsonSerialize() emits snake_case keys matching the JSON:API attribute names',
    function (): void {
        $provider = new Provider(
            id: '1',
            name: 'X',
            slug: 'x',
            simpleName: null,
            currency: 'USD',
            currencySymbol: '$',
            defaultSizeCode: 's-1',
            defaultRegionCode: 'r1',
        );

        expect($provider->jsonSerialize())->toBe([
            'id' => '1',
            'name' => 'X',
            'slug' => 'x',
            'simple_name' => null,
            'currency' => 'USD',
            'currency_symbol' => '$',
            'default_size_code' => 's-1',
            'default_region_code' => 'r1',
        ]);
    });
