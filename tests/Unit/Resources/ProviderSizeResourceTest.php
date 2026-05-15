<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use PhpDevKits\ForgeSdk\Data\ProviderSize;
use PhpDevKits\ForgeSdk\Forge;
use PhpDevKits\ForgeSdk\Requests\Providers\GetProviderSize;
use RuntimeException;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Tests\Utils\ForgeFixture;

test('get() returns a hydrated ProviderSize',
    /**
     * @throws Throwable
     */
    function (): void {
        $token = ($_ENV['FORGE_TEST_TOKEN'] ?? '') ?: 'test-token';
        $providerId = ($_ENV['FORGE_TEST_PROVIDER_ID'] ?? '') ?: '1';
        $sizeId = ($_ENV['FORGE_TEST_PROVIDER_SIZE_ID'] ?? '') ?: 'test-size-1';
        $mockClient = new MockClient([
            GetProviderSize::class => new ForgeFixture('providers/size-get'),
        ]);
        $forge = new Forge($token)->withMockClient($mockClient);

        $size = $forge->provider($providerId)->size($sizeId)->get();

        expect($size)->toBeInstanceOf(ProviderSize::class)
            ->and($size->ram)->toBeInt()->toBeGreaterThan(0);
    });

test('get() throws when the response has no data object',
    /**
     * @throws Throwable
     */
    function (): void {
        $mockClient = new MockClient([
            GetProviderSize::class => MockResponse::make(['meta' => []]),
        ]);
        $forge = new Forge('test-token')->withMockClient($mockClient);

        $forge->provider('digitalocean')->size('s-1')->get();
    })->throws(RuntimeException::class, 'Forge /providers/{slug}/sizes/{size} response did not include a `data` object.');
