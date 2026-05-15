<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use PhpDevKits\ForgeSdk\Data\ListProvidersOptions;
use PhpDevKits\ForgeSdk\Data\Page;
use PhpDevKits\ForgeSdk\Data\Provider;
use PhpDevKits\ForgeSdk\Forge;
use PhpDevKits\ForgeSdk\Requests\Providers\GetProviders;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Tests\Utils\ForgeFixture;

test('all() returns a Page<Provider>',
    /**
     * @throws Throwable
     */
    function (): void {
        $token = ($_ENV['FORGE_TEST_TOKEN'] ?? '') ?: 'test-token';
        $mockClient = new MockClient([
            GetProviders::class => new ForgeFixture('providers/list'),
        ]);
        $forge = new Forge($token)->withMockClient($mockClient);

        $page = $forge->providers()->all();

        expect($page)->toBeInstanceOf(Page::class)
            ->and($page->data)->not->toBeEmpty()
            ->and($page->data[0])->toBeInstanceOf(Provider::class);
    });

test('all() exposes pagination metadata from the response',
    /**
     * @throws Throwable
     */
    function (): void {
        $token = ($_ENV['FORGE_TEST_TOKEN'] ?? '') ?: 'test-token';
        $mockClient = new MockClient([
            GetProviders::class => new ForgeFixture('providers/list'),
        ]);
        $forge = new Forge($token)->withMockClient($mockClient);

        $page = $forge->providers()->all();

        expect($page->size)->toBeInt()->toBeGreaterThan(0);
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

        $collected = iterator_to_array($forge->providers()->iterate());

        expect($collected)->toBe([]);
    });

test('iterate() walks across next_cursor pages and stops on null cursor',
    /**
     * @throws Throwable
     */
    function (): void {
        $mockClient = new MockClient([
            MockResponse::make([
                'data' => [],
                'meta' => ['path' => 'x', 'per_page' => 0, 'next_cursor' => 'CURSOR-A', 'prev_cursor' => null],
            ]),
            MockResponse::make([
                'data' => [],
                'meta' => ['path' => 'x', 'per_page' => 0, 'next_cursor' => null, 'prev_cursor' => 'CURSOR-A'],
            ]),
        ]);
        $forge = new Forge('test-token')->withMockClient($mockClient);

        iterator_to_array($forge->providers()->iterate(new ListProvidersOptions(size: 5)));

        $query = $mockClient->getLastPendingRequest()?->query()->all() ?? [];

        expect($query)->toBe([
            'page[size]' => 5,
            'page[cursor]' => 'CURSOR-A',
        ]);
    });
