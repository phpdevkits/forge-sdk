<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use PhpDevKits\ForgeSdk\Data\ListOrganizationsOptions;
use PhpDevKits\ForgeSdk\Data\Organization;
use PhpDevKits\ForgeSdk\Data\Page;
use PhpDevKits\ForgeSdk\Forge;
use PhpDevKits\ForgeSdk\Requests\Organizations\GetOrganizations;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Tests\Utils\ForgeFixture;

test('all() returns a Page<Organization>',
    /**
     * @throws Throwable
     */
    function (): void {
        $mockClient = new MockClient([
            GetOrganizations::class => new ForgeFixture('organizations/list'),
        ]);
        $token = ($_ENV['FORGE_TEST_TOKEN'] ?? '') ?: 'test-token';
        $forge = new Forge($token)->withMockClient($mockClient);

        $page = $forge->organizations()->all();

        expect($page)->toBeInstanceOf(Page::class)
            ->and($page->data)->not->toBeEmpty()
            ->and($page->data[0])->toBeInstanceOf(Organization::class);
    });

test('all() exposes pagination metadata from the response',
    /**
     * @throws Throwable
     */
    function (): void {
        $mockClient = new MockClient([
            GetOrganizations::class => new ForgeFixture('organizations/list'),
        ]);
        $token = ($_ENV['FORGE_TEST_TOKEN'] ?? '') ?: 'test-token';
        $forge = new Forge($token)->withMockClient($mockClient);

        $page = $forge->organizations()->all();

        expect($page->size)->toBeInt()->toBeGreaterThan(0);
    });

test('iterate() walks across next_cursor pages and stops on null cursor',
    /*
     * Pagination control flow is tested with synthetic responses because real
     * cursor values aren't known at fixture-record time. The shape of the
     * response (data, meta.next_cursor) is exercised against real Forge data
     * in the all() test above.
     *
     * @throws Throwable
     */
    function (): void {
        $orgAttrs = static fn (string $slug): array => [
            'name' => $slug,
            'slug' => $slug,
            'created_at' => '2024-01-01T00:00:00Z',
            'updated_at' => '2024-01-01T00:00:00Z',
        ];

        $mockClient = new MockClient([
            MockResponse::make([
                'data' => [
                    ['id' => '1', 'type' => 'organizations', 'attributes' => $orgAttrs('first'), 'links' => ['self' => ['href' => 'x']]],
                ],
                'meta' => ['path' => 'x', 'per_page' => 1, 'next_cursor' => 'CURSOR-A', 'prev_cursor' => null],
            ]),
            MockResponse::make([
                'data' => [
                    ['id' => '2', 'type' => 'organizations', 'attributes' => $orgAttrs('second'), 'links' => ['self' => ['href' => 'x']]],
                ],
                'meta' => ['path' => 'x', 'per_page' => 1, 'next_cursor' => null, 'prev_cursor' => 'CURSOR-A'],
            ]),
        ]);
        $token = ($_ENV['FORGE_TEST_TOKEN'] ?? '') ?: 'test-token';
        $forge = new Forge($token)->withMockClient($mockClient);

        $collected = [];
        foreach ($forge->organizations()->iterate(new ListOrganizationsOptions(size: 1)) as $organization) {
            $collected[] = $organization->slug;
        }

        expect($collected)->toBe(['first', 'second']);
    });

test('iterate() forwards the next_cursor on the second request',
    /**
     * @throws Throwable
     */
    function (): void {
        $mockClient = new MockClient([
            MockResponse::make([
                'data' => [],
                'meta' => ['path' => 'x', 'per_page' => 0, 'next_cursor' => 'NEXT-PAGE-CURSOR', 'prev_cursor' => null],
            ]),
            MockResponse::make([
                'data' => [],
                'meta' => ['path' => 'x', 'per_page' => 0, 'next_cursor' => null, 'prev_cursor' => 'NEXT-PAGE-CURSOR'],
            ]),
        ]);
        $token = ($_ENV['FORGE_TEST_TOKEN'] ?? '') ?: 'test-token';
        $forge = new Forge($token)->withMockClient($mockClient);

        iterator_to_array($forge->organizations()->iterate(new ListOrganizationsOptions(size: 5)));

        $query = $mockClient->getLastPendingRequest()?->query()->all() ?? [];

        expect($query)->toBe([
            'page[size]' => 5,
            'page[cursor]' => 'NEXT-PAGE-CURSOR',
        ]);
    });
