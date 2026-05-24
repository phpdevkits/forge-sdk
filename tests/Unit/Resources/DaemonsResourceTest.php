<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use PhpDevKits\ForgeSdk\Data\Daemon;
use PhpDevKits\ForgeSdk\Data\Page;
use PhpDevKits\ForgeSdk\Forge;
use PhpDevKits\ForgeSdk\Requests\Servers\GetDaemons;
use Saloon\Http\Faking\MockClient;
use Tests\Utils\DaemonFixture;

test('all() returns a Page<Daemon>',
    /**
     * @throws Throwable
     */
    function (): void {
        $token = ($_ENV['FORGE_TEST_TOKEN'] ?? '') ?: 'test-token';
        $org = ($_ENV['FORGE_TEST_ORG_SLUG'] ?? '') ?: 'test-org';
        $serverId = ($_ENV['FORGE_TEST_SERVER_ID'] ?? '') ?: '1';
        $mockClient = new MockClient([
            GetDaemons::class => new DaemonFixture('daemons/list'),
        ]);
        $forge = new Forge($token, $org)->withMockClient($mockClient);

        $page = $forge->server($serverId)->daemons()->all();

        expect($page)->toBeInstanceOf(Page::class)
            ->and($page->data)->not->toBeEmpty()
            ->and($page->data[0])->toBeInstanceOf(Daemon::class);
    });

test('iterate() yields each daemon',
    /**
     * @throws Throwable
     */
    function (): void {
        $token = ($_ENV['FORGE_TEST_TOKEN'] ?? '') ?: 'test-token';
        $org = ($_ENV['FORGE_TEST_ORG_SLUG'] ?? '') ?: 'test-org';
        $serverId = ($_ENV['FORGE_TEST_SERVER_ID'] ?? '') ?: '1';
        $mockClient = new MockClient([
            GetDaemons::class => new DaemonFixture('daemons/list'),
        ]);
        $forge = new Forge($token, $org)->withMockClient($mockClient);

        $daemons = iterator_to_array($forge->server($serverId)->daemons()->iterate());

        expect($daemons)->not->toBeEmpty()
            ->and($daemons[0])->toBeInstanceOf(Daemon::class);
    });
