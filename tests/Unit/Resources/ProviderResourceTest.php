<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use PhpDevKits\ForgeSdk\Data\Provider;
use PhpDevKits\ForgeSdk\Forge;
use PhpDevKits\ForgeSdk\Requests\Providers\GetProvider;
use PhpDevKits\ForgeSdk\Resources\ProviderRegionResource;
use PhpDevKits\ForgeSdk\Resources\ProviderRegionsResource;
use PhpDevKits\ForgeSdk\Resources\ProviderSizeResource;
use PhpDevKits\ForgeSdk\Resources\ProviderSizesResource;
use RuntimeException;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Tests\Utils\ForgeFixture;

test('get() returns a hydrated Provider',
    /**
     * @throws Throwable
     */
    function (): void {
        $token = ($_ENV['FORGE_TEST_TOKEN'] ?? '') ?: 'test-token';
        $providerId = ($_ENV['FORGE_TEST_PROVIDER_ID'] ?? '') ?: '1';
        $mockClient = new MockClient([
            GetProvider::class => new ForgeFixture('providers/get'),
        ]);
        $forge = new Forge($token)->withMockClient($mockClient);

        $provider = $forge->provider($providerId)->get();

        expect($provider)->toBeInstanceOf(Provider::class)
            ->and($provider->id)->toBeString()->not->toBeEmpty()
            ->and($provider->slug)->toBeString()->not->toBeEmpty();
    });

test('get() throws when the response has no data object',
    /**
     * @throws Throwable
     */
    function (): void {
        $mockClient = new MockClient([
            GetProvider::class => MockResponse::make(['meta' => []]),
        ]);
        $forge = new Forge('test-token')->withMockClient($mockClient);

        $forge->provider('digitalocean')->get();
    })->throws(RuntimeException::class, 'Forge /providers/{slug} response did not include a `data` object.');

test('regions() returns a ProviderRegionsResource',
    function (): void {
        $forge = new Forge('test-token');

        expect($forge->provider('digitalocean')->regions())->toBeInstanceOf(ProviderRegionsResource::class);
    });

test('region($id) returns a ProviderRegionResource',
    function (): void {
        $forge = new Forge('test-token');

        expect($forge->provider('digitalocean')->region('nyc3'))->toBeInstanceOf(ProviderRegionResource::class);
    });

test('sizes() returns a ProviderSizesResource',
    function (): void {
        $forge = new Forge('test-token');

        expect($forge->provider('digitalocean')->sizes())->toBeInstanceOf(ProviderSizesResource::class);
    });

test('size($id) returns a ProviderSizeResource',
    function (): void {
        $forge = new Forge('test-token');

        expect($forge->provider('digitalocean')->size('s-1vcpu-1gb'))->toBeInstanceOf(ProviderSizeResource::class);
    });
