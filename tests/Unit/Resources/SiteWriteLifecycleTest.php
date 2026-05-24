<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use PhpDevKits\ForgeSdk\Data\CreateSiteData;
use PhpDevKits\ForgeSdk\Data\Site;
use PhpDevKits\ForgeSdk\Data\UpdateSiteData;
use PhpDevKits\ForgeSdk\Enums\SiteType;
use PhpDevKits\ForgeSdk\Forge;
use PhpDevKits\ForgeSdk\Requests\Servers\CreateSite;
use PhpDevKits\ForgeSdk\Requests\Servers\DeleteSite;
use PhpDevKits\ForgeSdk\Requests\Servers\GetSite;
use PhpDevKits\ForgeSdk\Requests\Servers\GetSites;
use PhpDevKits\ForgeSdk\Requests\Servers\UpdateSite;
use Saloon\Http\Faking\MockClient;
use Tests\Utils\SiteFixture;
use Throwable;

/*
 * End-to-end Sites lifecycle: create -> update -> list -> get -> delete,
 * against the configured Forge server (recording) or replayed from
 * committed fixtures otherwise. try/finally guards cleanup so a partway
 * failure during recording doesn't leak a real site.
 *
 * Forge provisions an on-forge site asynchronously. During the provisioning
 * window the individual-site routes (GET/PUT /sites/{id}) intermittently
 * return 405. The SDK can't poll for readiness in record mode (Saloon would
 * replay the first recorded response), so when recording we wait on a
 * SEPARATE un-mocked connector until the real site is GET-able, then drive
 * the recorded calls. On replay (fixtures present) the wait is skipped.
 *
 * Required env vars (recording only): FORGE_TEST_TOKEN,
 * FORGE_TEST_ORG_SLUG, FORGE_TEST_SERVER_ID.
 */
test('lifecycle: create -> update -> list -> get -> delete',
    /**
     * @throws Throwable
     */
    function (): void {
        $token = ($_ENV['FORGE_TEST_TOKEN'] ?? '') ?: 'test-token';
        $org = ($_ENV['FORGE_TEST_ORG_SLUG'] ?? '') ?: 'test-org';
        $serverId = ($_ENV['FORGE_TEST_SERVER_ID'] ?? '') ?: '1';

        // on-forge sites take a single dotless subdomain label.
        $name = 'sdk-test-'.bin2hex(random_bytes(3));

        $recording = ! file_exists(__DIR__.'/../../Fixtures/Saloon/sites/get.json');

        $mockClient = new MockClient([
            CreateSite::class => new SiteFixture('sites/create'),
            GetSites::class => new SiteFixture('sites/list'),
            GetSite::class => new SiteFixture('sites/get'),
            UpdateSite::class => new SiteFixture('sites/update'),
            DeleteSite::class => new SiteFixture('sites/delete'),
        ]);
        $forge = new Forge($token, $org)->withMockClient($mockClient);

        $createdId = null;
        try {
            $created = $forge->server($serverId)->sites()->create(
                new CreateSiteData(type: SiteType::Php, name: $name, domainMode: 'on-forge'),
            );
            $createdId = $created->id;

            expect($created)->toBeInstanceOf(Site::class)
                ->and($created->id)->toBeString()->not->toBeEmpty();

            // Wait for the real site to finish provisioning before exercising
            // the individual-site routes (they 405 mid-install). A live,
            // un-mocked connector observes real state; skipped on replay,
            // where Saloon would just replay the first recorded response.
            if ($recording) {
                $live = new Forge($token, $org);
                for ($attempt = 0; $attempt < 60; $attempt++) {
                    try {
                        $live->server($serverId)->site($createdId)->get();
                        break;
                    } catch (Throwable) {
                        sleep(2);
                    }
                }
            }

            // PUT returns 202 with an empty body, so update() is void.
            $forge->server($serverId)->site($createdId)->update(
                new UpdateSiteData(directory: '/public'),
            );

            $page = $forge->server($serverId)->sites()->all();
            expect($page->data)->not->toBeEmpty();

            $fetched = $forge->server($serverId)->site($createdId)->get();
            expect($fetched)->toBeInstanceOf(Site::class)
                ->and($fetched->id)->toBeString()->not->toBeEmpty();

            $forge->server($serverId)->site($createdId)->delete();
            $createdId = null;
        } finally {
            if ($createdId !== null) {
                try {
                    $forge->server($serverId)->site($createdId)->delete();
                } catch (Throwable) {
                    // best-effort cleanup; original failure surfaces
                }
            }
        }
    });
