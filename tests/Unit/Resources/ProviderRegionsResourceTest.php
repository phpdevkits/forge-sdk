<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use PhpDevKits\ForgeSdk\Data\ListProviderRegionsOptions;
use PhpDevKits\ForgeSdk\Data\Page;
use PhpDevKits\ForgeSdk\Data\ProviderRegion;
use PhpDevKits\ForgeSdk\Forge;
use PhpDevKits\ForgeSdk\Requests\Providers\GetProviderRegions;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Tests\Utils\ForgeFixture;

test('all() returns a Page<ProviderRegion>',
    /**
     * @throws Throwable
     */
    function (): void {
        $token = ($_ENV['FORGE_TEST_TOKEN'] ?? '') ?: 'test-token';
        $providerId = ($_ENV['FORGE_TEST_PROVIDER_ID'] ?? '') ?: '1';
        $mockClient = new MockClient([
            GetProviderRegions::class => new ForgeFixture('providers/regions-list'),
        ]);
        $forge = new Forge($token)->withMockClient($mockClient);

        $page = $forge->provider($providerId)->regions()->all();

        expect($page)->toBeInstanceOf(Page::class)
            ->and($page->data)->not->toBeEmpty()
            ->and($page->data[0])->toBeInstanceOf(ProviderRegion::class);
    });

test('iterate() defaults to fresh options when called with no arguments',
    /**
     * @throws Throwable
     */
    function (): void {
        $mockClient = new MockClient([
            MockResponse::make(['data' => [], 'meta' => ['path' => 'x', 'per_page' => 0, 'next_cursor' => null, 'prev_cursor' => null]]),
        ]);
        $forge = new Forge('test-token')->withMockClient($mockClient);

        $collected = iterator_to_array($forge->provider('1')->regions()->iterate());

        expect($collected)->toBe([]);
    });

test('iterate() forwards the next_cursor on the second request',
    /**
     * @throws Throwable
     */
    function (): void {
        $mockClient = new MockClient([
            MockResponse::make([
                'data' => [],
                'meta' => ['path' => 'x', 'per_page' => 0, 'next_cursor' => 'CURSOR-X', 'prev_cursor' => null],
            ]),
            MockResponse::make([
                'data' => [],
                'meta' => ['path' => 'x', 'per_page' => 0, 'next_cursor' => null, 'prev_cursor' => 'CURSOR-X'],
            ]),
        ]);
        $forge = new Forge('test-token')->withMockClient($mockClient);

        iterator_to_array($forge->provider('digitalocean')->regions()->iterate(new ListProviderRegionsOptions(size: 3)));

        $query = $mockClient->getLastPendingRequest()?->query()->all() ?? [];

        expect($query)->toBe([
            'page[size]' => 3,
            'page[cursor]' => 'CURSOR-X',
        ]);
    });
