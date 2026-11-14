<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use PhpDevKits\ForgeSdk\Data\CreateScheduledJobData;
use PhpDevKits\ForgeSdk\Data\Page;
use PhpDevKits\ForgeSdk\Data\ScheduledJob;
use PhpDevKits\ForgeSdk\Enums\CronFrequency;
use PhpDevKits\ForgeSdk\Forge;
use PhpDevKits\ForgeSdk\Requests\Sites\CreateScheduledJob;
use PhpDevKits\ForgeSdk\Requests\Sites\DeleteScheduledJob;
use PhpDevKits\ForgeSdk\Requests\Sites\GetScheduledJob;
use PhpDevKits\ForgeSdk\Requests\Sites\GetScheduledJobOutput;
use PhpDevKits\ForgeSdk\Requests\Sites\GetScheduledJobs;
use Saloon\Http\Faking\MockClient;
use Tests\Utils\ScheduledJobFixture;
use Throwable;

/*
 * End-to-end site-scoped scheduled-job lifecycle: create -> list -> iterate ->
 * get -> output -> delete, against the configured site (recording) or replayed
 * from committed fixtures otherwise.
 *
 * The job runs a harmless `echo` nightly and is removed at the end; the list
 * is recorded after the create so it contains the job. try/finally guarantees
 * cleanup if any step fails during recording.
 *
 * Required env vars (recording only): FORGE_TEST_TOKEN, FORGE_TEST_ORG_SLUG,
 * FORGE_TEST_SERVER_ID, FORGE_TEST_SITE_ID.
 */
test('lifecycle: create -> list -> iterate -> get -> output -> delete (site scope)',
    /**
     * @throws Throwable
     */
    function (): void {
        $token = ($_ENV['FORGE_TEST_TOKEN'] ?? '') ?: 'test-token';
        $org = ($_ENV['FORGE_TEST_ORG_SLUG'] ?? '') ?: 'test-org';
        $serverId = ($_ENV['FORGE_TEST_SERVER_ID'] ?? '') ?: '1';
        $siteId = ($_ENV['FORGE_TEST_SITE_ID'] ?? '') ?: '1';

        $mockClient = new MockClient([
            CreateScheduledJob::class => new ScheduledJobFixture('site-scheduled-jobs/create'),
            GetScheduledJobs::class => new ScheduledJobFixture('site-scheduled-jobs/list'),
            GetScheduledJob::class => new ScheduledJobFixture('site-scheduled-jobs/get'),
            GetScheduledJobOutput::class => new ScheduledJobFixture('site-scheduled-jobs/output'),
            DeleteScheduledJob::class => new ScheduledJobFixture('site-scheduled-jobs/delete'),
        ]);
        $forge = new Forge($token, $org)->withMockClient($mockClient);

        $createdId = null;
        try {
            $created = $forge->server($serverId)->site($siteId)->scheduledJobs()->create(
                new CreateScheduledJobData(
                    command: 'echo sdk-test',
                    user: 'forge',
                    frequency: CronFrequency::Nightly,
                    name: 'sdk-test-site-job',
                ),
            );
            $createdId = $created->id;

            expect($created)->toBeInstanceOf(ScheduledJob::class)
                ->and($created->id)->toBeString()->not->toBeEmpty();

            $page = $forge->server($serverId)->site($siteId)->scheduledJobs()->all();

            expect($page)->toBeInstanceOf(Page::class)
                ->and($page->data)->not->toBeEmpty()
                ->and($page->data[0])->toBeInstanceOf(ScheduledJob::class);

            $iterated = iterator_to_array($forge->server($serverId)->site($siteId)->scheduledJobs()->iterate());

            expect($iterated)->not->toBeEmpty();

            $fetched = $forge->server($serverId)->site($siteId)->scheduledJob($createdId)->get();

            expect($fetched)->toBeInstanceOf(ScheduledJob::class);

            expect($forge->server($serverId)->site($siteId)->scheduledJob($createdId)->output())->toBeString();

            $forge->server($serverId)->site($siteId)->scheduledJob($createdId)->delete();
            $createdId = null;
        } finally {
            if ($createdId !== null) {
                try {
                    $forge->server($serverId)->site($siteId)->scheduledJob($createdId)->delete();
                } catch (Throwable) {
                    // best-effort cleanup; original failure surfaces
                }
            }
        }
    });
