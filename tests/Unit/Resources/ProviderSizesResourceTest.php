<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use PhpDevKits\ForgeSdk\Data\ListProviderSizesOptions;
use PhpDevKits\ForgeSdk\Data\Page;
use PhpDevKits\ForgeSdk\Data\ProviderSize;
use PhpDevKits\ForgeSdk\Forge;
use PhpDevKits\ForgeSdk\Requests\Providers\GetProviderSizes;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Tests\Utils\ForgeFixture;

test('all() returns a Page<ProviderSize> region-independent',
    /**
     * @throws Throwable
     */
    function (): void {
        $token = ($_ENV['FORGE_TEST_TOKEN'] ?? '') ?: 'test-token';
        $providerId = ($_ENV['FORGE_TEST_PROVIDER_ID'] ?? '') ?: '1';
        $mockClient = new MockClient([
            GetProviderSizes::class => new ForgeFixture('providers/sizes-list'),
        ]);
        $forge = new Forge($token)->withMockClient($mockClient);

        $page = $forge->provider($providerId)->sizes()->all();

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

        $collected = iterator_to_array($forge->provider('1')->sizes()->iterate());

        expect($collected)->toBe([]);
    });

test('iterate() walks across next_cursor pages and yields each size',
    /**
     * @throws Throwable
     */
    function (): void {
        $sizeAttrs = static fn (string $code): array => [
            'name' => $code, 'code' => $code, 'series' => 's', 'category' => 'c',
            'cpus' => 1, 'disk_type' => 'ssd', 'architecture' => null,
            'ram' => 1024, 'disk' => 25600,
        ];

        $mockClient = new MockClient([
            MockResponse::make([
                'data' => [['id' => '1', 'type' => 'providerSizes', 'attributes' => $sizeAttrs('s-1')]],
                'meta' => ['path' => 'x', 'per_page' => 1, 'next_cursor' => 'CURSOR-Z', 'prev_cursor' => null],
            ]),
            MockResponse::make([
                'data' => [['id' => '2', 'type' => 'providerSizes', 'attributes' => $sizeAttrs('s-2')]],
                'meta' => ['path' => 'x', 'per_page' => 1, 'next_cursor' => null, 'prev_cursor' => 'CURSOR-Z'],
            ]),
        ]);
        $forge = new Forge('test-token')->withMockClient($mockClient);

        $codes = [];
        foreach ($forge->provider('1')->sizes()->iterate(new ListProviderSizesOptions(size: 1)) as $size) {
            $codes[] = $size->code;
        }

        $query = $mockClient->getLastPendingRequest()?->query()->all() ?? [];

        expect($codes)->toBe(['s-1', 's-2'])
            ->and($query)->toBe([
                'page[size]' => 1,
                'page[cursor]' => 'CURSOR-Z',
            ]);
    });
