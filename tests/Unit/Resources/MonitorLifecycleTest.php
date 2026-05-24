<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use PhpDevKits\ForgeSdk\Data\CreateMonitorData;
use PhpDevKits\ForgeSdk\Data\Monitor;
use PhpDevKits\ForgeSdk\Data\Page;
use PhpDevKits\ForgeSdk\Enums\MonitorMetricType;
use PhpDevKits\ForgeSdk\Enums\MonitorOperator;
use PhpDevKits\ForgeSdk\Forge;
use PhpDevKits\ForgeSdk\Requests\Servers\CreateMonitor;
use PhpDevKits\ForgeSdk\Requests\Servers\DeleteMonitor;
use PhpDevKits\ForgeSdk\Requests\Servers\GetMonitor;
use PhpDevKits\ForgeSdk\Requests\Servers\GetMonitors;
use Saloon\Http\Faking\MockClient;
use Tests\Utils\MonitorFixture;
use Throwable;

/*
 * End-to-end monitor lifecycle: create -> list -> iterate -> get -> delete,
 * against the configured server (recording) or replayed from fixtures.
 *
 * The monitor watches disk usage >= 95% (harmless) and is removed at the end;
 * the list is recorded after the create so it contains the monitor.
 * try/finally guarantees cleanup if a step fails during recording.
 *
 * Required env vars (recording only): FORGE_TEST_TOKEN, FORGE_TEST_ORG_SLUG,
 * FORGE_TEST_SERVER_ID.
 */
test('lifecycle: create -> list -> iterate -> get -> delete',
    /**
     * @throws Throwable
     */
    function (): void {
        $token = ($_ENV['FORGE_TEST_TOKEN'] ?? '') ?: 'test-token';
        $org = ($_ENV['FORGE_TEST_ORG_SLUG'] ?? '') ?: 'test-org';
        $serverId = ($_ENV['FORGE_TEST_SERVER_ID'] ?? '') ?: '1';

        $mockClient = new MockClient([
            CreateMonitor::class => new MonitorFixture('monitors/create'),
            GetMonitors::class => new MonitorFixture('monitors/list'),
            GetMonitor::class => new MonitorFixture('monitors/get'),
            DeleteMonitor::class => new MonitorFixture('monitors/delete'),
        ]);
        $forge = new Forge($token, $org)->withMockClient($mockClient);

        $createdId = null;
        try {
            $created = $forge->server($serverId)->monitors()->create(
                new CreateMonitorData(
                    type: MonitorMetricType::Disk,
                    operator: MonitorOperator::GreaterThanOrEqual,
                    threshold: 95.0,
                    notify: 'sdk-test@example.com',
                    minutes: 5,
                ),
            );
            $createdId = $created->id;

            expect($created)->toBeInstanceOf(Monitor::class)
                ->and($created->id)->toBeString()->not->toBeEmpty();

            $page = $forge->server($serverId)->monitors()->all();
            expect($page)->toBeInstanceOf(Page::class)
                ->and($page->data)->not->toBeEmpty()
                ->and($page->data[0])->toBeInstanceOf(Monitor::class);

            $iterated = iterator_to_array($forge->server($serverId)->monitors()->iterate());
            expect($iterated)->not->toBeEmpty();

            $fetched = $forge->server($serverId)->monitor($createdId)->get();
            expect($fetched)->toBeInstanceOf(Monitor::class);

            $forge->server($serverId)->monitor($createdId)->delete();
            $createdId = null;
        } finally {
            if ($createdId !== null) {
                try {
                    $forge->server($serverId)->monitor($createdId)->delete();
                } catch (Throwable) {
                    // best-effort cleanup; original failure surfaces
                }
            }
        }
    });
