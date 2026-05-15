<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Providers;

use PhpDevKits\ForgeSdk\Forge;
use PhpDevKits\ForgeSdk\Requests\Providers\GetProviderRegion;
use Saloon\Http\Faking\MockClient;
use Tests\Utils\ForgeFixture;

test('sends a GET to /providers/{slug}/regions/{region}',
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

        $forge->provider($providerId)->region($regionId)->get();

        $url = $mockClient->getLastPendingRequest()?->getUrl() ?? '';

        expect($url)->toBe('https://forge.laravel.com/api/providers/'.$providerId.'/regions/'.$regionId);
    });
