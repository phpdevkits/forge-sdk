<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use PhpDevKits\ForgeSdk\Forge;
use PhpDevKits\ForgeSdk\Requests\Servers\DeleteSiteLog;
use PhpDevKits\ForgeSdk\Requests\Servers\GetSiteLog;
use Saloon\Http\Faking\MockClient;
use Tests\Utils\SiteLogFixture;

/*
 * Site Nginx log read + clear, against the configured site (recording) or
 * replayed from committed fixtures otherwise.
 *
 * Recording reads each log then clears it (DELETE empties the log async). The
 * fixture overwrites the opaque log `content`. Access and error are split into
 * separate tests so each records its own fixture (both share the GetSiteLog /
 * DeleteSiteLog request classes, distinguished only by the SiteLog enum).
 *
 * Required env vars (recording only): FORGE_TEST_TOKEN, FORGE_TEST_ORG_SLUG,
 * FORGE_TEST_SERVER_ID, FORGE_TEST_SITE_ID.
 */
test('reads and clears the Nginx access log',
    /**
     * @throws Throwable
     */
    function (): void {
        $token = ($_ENV['FORGE_TEST_TOKEN'] ?? '') ?: 'test-token';
        $org = ($_ENV['FORGE_TEST_ORG_SLUG'] ?? '') ?: 'test-org';
        $serverId = ($_ENV['FORGE_TEST_SERVER_ID'] ?? '') ?: '1';
        $siteId = ($_ENV['FORGE_TEST_SITE_ID'] ?? '') ?: '1';

        $mockClient = new MockClient([
            GetSiteLog::class => new SiteLogFixture('logs/site-nginx-access'),
            DeleteSiteLog::class => new SiteLogFixture('logs/delete-nginx-access'),
        ]);
        $forge = new Forge($token, $org)->withMockClient($mockClient);

        $logs = $forge->server($serverId)->site($siteId)->logs();

        expect($logs->nginxAccess())->toBeString();

        $logs->deleteNginxAccess();
    });

test('reads and clears the Nginx error log',
    /**
     * @throws Throwable
     */
    function (): void {
        $token = ($_ENV['FORGE_TEST_TOKEN'] ?? '') ?: 'test-token';
        $org = ($_ENV['FORGE_TEST_ORG_SLUG'] ?? '') ?: 'test-org';
        $serverId = ($_ENV['FORGE_TEST_SERVER_ID'] ?? '') ?: '1';
        $siteId = ($_ENV['FORGE_TEST_SITE_ID'] ?? '') ?: '1';

        $mockClient = new MockClient([
            GetSiteLog::class => new SiteLogFixture('logs/site-nginx-error'),
            DeleteSiteLog::class => new SiteLogFixture('logs/delete-nginx-error'),
        ]);
        $forge = new Forge($token, $org)->withMockClient($mockClient);

        $logs = $forge->server($serverId)->site($siteId)->logs();

        expect($logs->nginxError())->toBeString();

        $logs->deleteNginxError();
    });
