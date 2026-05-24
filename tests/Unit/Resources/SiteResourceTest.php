<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use PhpDevKits\ForgeSdk\Data\Site;
use PhpDevKits\ForgeSdk\Forge;
use PhpDevKits\ForgeSdk\Requests\Servers\GetSite;
use Saloon\Http\Faking\MockClient;
use Tests\Utils\SiteFixture;

test('get() returns a hydrated Site',
    /**
     * @throws Throwable
     */
    function (): void {
        $token = ($_ENV['FORGE_TEST_TOKEN'] ?? '') ?: 'test-token';
        $org = ($_ENV['FORGE_TEST_ORG_SLUG'] ?? '') ?: 'test-org';
        $serverId = ($_ENV['FORGE_TEST_SERVER_ID'] ?? '') ?: '1';
        $siteId = ($_ENV['FORGE_TEST_SITE_ID'] ?? '') ?: '1';
        $mockClient = new MockClient([
            GetSite::class => new SiteFixture('sites/get'),
        ]);
        $forge = new Forge($token, $org)->withMockClient($mockClient);

        $site = $forge->server($serverId)->site($siteId)->get();

        expect($site)->toBeInstanceOf(Site::class)
            ->and($site->id)->toBeString()->not->toBeEmpty()
            ->and($site->name)->toBeString()->not->toBeEmpty();
    });
