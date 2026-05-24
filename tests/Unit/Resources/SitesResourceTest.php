<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use PhpDevKits\ForgeSdk\Data\Page;
use PhpDevKits\ForgeSdk\Data\Site;
use PhpDevKits\ForgeSdk\Forge;
use PhpDevKits\ForgeSdk\Requests\Servers\GetSites;
use Saloon\Http\Faking\MockClient;
use Tests\Utils\SiteFixture;

test('all() returns a Page<Site>',
    /**
     * @throws Throwable
     */
    function (): void {
        $token = ($_ENV['FORGE_TEST_TOKEN'] ?? '') ?: 'test-token';
        $org = ($_ENV['FORGE_TEST_ORG_SLUG'] ?? '') ?: 'test-org';
        $serverId = ($_ENV['FORGE_TEST_SERVER_ID'] ?? '') ?: '1';
        $mockClient = new MockClient([
            GetSites::class => new SiteFixture('sites/list'),
        ]);
        $forge = new Forge($token, $org)->withMockClient($mockClient);

        $page = $forge->server($serverId)->sites()->all();

        expect($page)->toBeInstanceOf(Page::class)
            ->and($page->data)->not->toBeEmpty()
            ->and($page->data[0])->toBeInstanceOf(Site::class);
    });

test('iterate() yields each site returned by the API',
    /**
     * @throws Throwable
     */
    function (): void {
        $token = ($_ENV['FORGE_TEST_TOKEN'] ?? '') ?: 'test-token';
        $org = ($_ENV['FORGE_TEST_ORG_SLUG'] ?? '') ?: 'test-org';
        $serverId = ($_ENV['FORGE_TEST_SERVER_ID'] ?? '') ?: '1';
        $mockClient = new MockClient([
            GetSites::class => new SiteFixture('sites/list'),
        ]);
        $forge = new Forge($token, $org)->withMockClient($mockClient);

        $sites = iterator_to_array($forge->server($serverId)->sites()->iterate());

        expect($sites)->not->toBeEmpty()
            ->and($sites[0])->toBeInstanceOf(Site::class);
    });
