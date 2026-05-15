<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use PhpDevKits\ForgeSdk\Data\ListProviderRegionSizesOptions;
use PhpDevKits\ForgeSdk\Data\Page;
use PhpDevKits\ForgeSdk\Data\ProviderSize;
use PhpDevKits\ForgeSdk\Forge;
use PhpDevKits\ForgeSdk\Requests\Providers\GetProviderRegionSizes;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Tests\Utils\ForgeFixture;

test('all() returns a Page<ProviderSize> for a given region',
    /**
     * @throws Throwable
     */
    function (): void {
        $token = ($_ENV['FORGE_TEST_TOKEN'] ?? '') ?: 'test-token';
        $providerId = ($_ENV['FORGE_TEST_PROVIDER_ID'] ?? '') ?: '1';
        $regionId = ($_ENV['FORGE_TEST_PROVIDER_REGION_ID'] ?? '') ?: 'test-region-1';
        $mockClient = new MockClient([
            GetProviderRegionSizes::class => new ForgeFixture('providers/region-sizes-list'),
        ]);
        $forge = new Forge($token)->withMockClient($mockClient);

        $page = $forge->provider($providerId)->region($regionId)->sizes()->all();

        expect($page)->toBeInstanceOf(Page::class)
            ->and($page->data)->not->toBeEmpty()
            ->and($page->data[0])->toBeInstanceOf(ProviderSize::class);
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

        $collected = iterator_to_array($forge->provider('1')->region('1')->sizes()->iterate());

        expect($collected)->toBe([]);
    });

test('iterate() walks across next_cursor pages and yields each size',
    /**
     * @throws Throwable
     */
    function (): void {
        $mockClient = new MockClient([
            MockResponse::make([
                'data' => [['id' => '11', 'type' => 'providerSizes', 'attributes' => ['name' => 'sparse-1']]],
                'meta' => ['path' => 'x', 'per_page' => 1, 'next_cursor' => 'CURSOR-Y', 'prev_cursor' => null],
            ]),
            MockResponse::make([
                'data' => [['id' => '12', 'type' => 'providerSizes', 'attributes' => ['name' => 'sparse-2']]],
                'meta' => ['path' => 'x', 'per_page' => 1, 'next_cursor' => null, 'prev_cursor' => 'CURSOR-Y'],
            ]),
        ]);
        $forge = new Forge('test-token')->withMockClient($mockClient);

        $names = [];
        foreach ($forge->provider('1')->region('1')->sizes()->iterate(new ListProviderRegionSizesOptions(size: 1)) as $size) {
            $names[] = $size->name;
        }

        $query = $mockClient->getLastPendingRequest()?->query()->all() ?? [];

        expect($names)->toBe(['sparse-1', 'sparse-2'])
            ->and($query)->toBe([
                'page[size]' => 1,
                'page[cursor]' => 'CURSOR-Y',
            ]);
    });
