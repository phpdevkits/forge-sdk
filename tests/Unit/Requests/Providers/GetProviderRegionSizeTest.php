<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Providers;

use PhpDevKits\ForgeSdk\Forge;
use PhpDevKits\ForgeSdk\Requests\Providers\GetProviderRegionSize;
use Saloon\Http\Faking\MockClient;
use Tests\Utils\ForgeFixture;

test('sends a GET to /providers/{slug}/regions/{region}/sizes/{size}',
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

        $forge->provider($providerId)->region($regionId)->size($sizeId)->get();

        $url = $mockClient->getLastPendingRequest()?->getUrl() ?? '';

        expect($url)->toBe('https://forge.laravel.com/api/providers/'.$providerId.'/regions/'.$regionId.'/sizes/'.$sizeId);
    });
