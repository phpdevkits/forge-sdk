<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use PhpDevKits\ForgeSdk\Data\ProviderSize;
use PhpDevKits\ForgeSdk\Forge;
use PhpDevKits\ForgeSdk\Requests\Providers\GetProviderRegionSize;
use RuntimeException;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Tests\Utils\ForgeFixture;

test('get() returns a hydrated ProviderSize for a region/size pair',
    /**
     * @throws Throwable
     */
    function (): void {
        $token = ($_ENV['FORGE_TEST_TOKEN'] ?? '') ?: 'test-token';
        $providerId = ($_ENV['FORGE_TEST_PROVIDER_ID'] ?? '') ?: '1';
        $regionId = ($_ENV['FORGE_TEST_PROVIDER_REGION_ID'] ?? '') ?: 'test-region-1';
        $sizeId = ($_ENV['FORGE_TEST_PROVIDER_REGION_SIZE_ID'] ?? '') ?: 'test-size-1';
        $mockClient = new MockClient([
            GetProviderRegionSize::class => new ForgeFixture('providers/region-size-get'),
        ]);
        $forge = new Forge($token)->withMockClient($mockClient);

        $size = $forge->provider($providerId)->region($regionId)->size($sizeId)->get();

        // Region-scoped size endpoint returns sparse attributes — only `id`
        // and `name` are guaranteed. Full attributes live on the provider-scoped
        // `/providers/{id}/sizes/{size}` endpoint, exercised by ProviderSizeResourceTest.
        expect($size)->toBeInstanceOf(ProviderSize::class)
            ->and($size->id)->toBeString()->not->toBeEmpty()
            ->and($size->name)->toBeString()->not->toBeEmpty();
    });

test('get() throws when the response has no data object',
    /**
     * @throws Throwable
     */
    function (): void {
        $mockClient = new MockClient([
            GetProviderRegionSize::class => MockResponse::make(['meta' => []]),
        ]);
        $forge = new Forge('test-token')->withMockClient($mockClient);

        $forge->provider('digitalocean')->region('nyc3')->size('s-1')->get();
    })->throws(RuntimeException::class, 'Forge /providers/{slug}/regions/{region}/sizes/{size} response did not include a `data` object.');
