<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use PhpDevKits\ForgeSdk\Data\ProviderRegion;
use PhpDevKits\ForgeSdk\Forge;
use PhpDevKits\ForgeSdk\Requests\Providers\GetProviderRegion;
use PhpDevKits\ForgeSdk\Resources\ProviderRegionSizeResource;
use PhpDevKits\ForgeSdk\Resources\ProviderRegionSizesResource;
use RuntimeException;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Tests\Utils\ForgeFixture;

test('get() returns a hydrated ProviderRegion',
    /**
     * @throws Throwable
     */
    function (): void {
        $token = ($_ENV['FORGE_TEST_TOKEN'] ?? '') ?: 'test-token';
        $providerId = ($_ENV['FORGE_TEST_PROVIDER_ID'] ?? '') ?: '1';
        $regionId = ($_ENV['FORGE_TEST_PROVIDER_REGION_ID'] ?? '') ?: 'test-region-1';
        $mockClient = new MockClient([
            GetProviderRegion::class => new ForgeFixture('providers/region-get'),
        ]);
        $forge = new Forge($token)->withMockClient($mockClient);

        $region = $forge->provider($providerId)->region($regionId)->get();

        expect($region)->toBeInstanceOf(ProviderRegion::class)
            ->and($region->code)->toBeString()->not->toBeEmpty();
    });

test('get() throws when the response has no data object',
    /**
     * @throws Throwable
     */
    function (): void {
        $mockClient = new MockClient([
            GetProviderRegion::class => MockResponse::make(['meta' => []]),
        ]);
        $forge = new Forge('test-token')->withMockClient($mockClient);

        $forge->provider('digitalocean')->region('nyc3')->get();
    })->throws(RuntimeException::class, 'Forge /providers/{slug}/regions/{region} response did not include a `data` object.');

test('sizes() returns a ProviderRegionSizesResource',
    function (): void {
        $forge = new Forge('test-token');

        expect($forge->provider('digitalocean')->region('nyc3')->sizes())->toBeInstanceOf(ProviderRegionSizesResource::class);
    });

test('size($id) returns a ProviderRegionSizeResource',
    function (): void {
        $forge = new Forge('test-token');

        expect($forge->provider('digitalocean')->region('nyc3')->size('s-1'))->toBeInstanceOf(ProviderRegionSizeResource::class);
    });
