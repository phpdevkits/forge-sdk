<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use PhpDevKits\ForgeSdk\Data\ListServersOptions;
use PhpDevKits\ForgeSdk\Data\Page;
use PhpDevKits\ForgeSdk\Data\Server;
use PhpDevKits\ForgeSdk\Forge;
use PhpDevKits\ForgeSdk\Requests\Servers\GetServers;
use Saloon\Http\Faking\MockClient;
use Tests\Utils\ServerFixture;

test('all() returns a Page<Server>',
    /**
     * @throws Throwable
     */
    function (): void {
        $token = ($_ENV['FORGE_TEST_TOKEN'] ?? '') ?: 'test-token';
        $org = ($_ENV['FORGE_TEST_ORG_SLUG'] ?? '') ?: 'test-org';
        $mockClient = new MockClient([
            GetServers::class => new ServerFixture('servers/list'),
        ]);
        $forge = new Forge($token, $org)->withMockClient($mockClient);

        $page = $forge->servers()->all();

        expect($page)->toBeInstanceOf(Page::class)
            ->and($page->data)->not->toBeEmpty()
            ->and($page->data[0])->toBeInstanceOf(Server::class);
    });

test('iterate() with no arguments yields every server in the org',
    /**
     * @throws Throwable
     */
    function (): void {
        $token = ($_ENV['FORGE_TEST_TOKEN'] ?? '') ?: 'test-token';
        $org = ($_ENV['FORGE_TEST_ORG_SLUG'] ?? '') ?: 'test-org';
        $mockClient = new MockClient([
            GetServers::class => new ServerFixture('servers/list'),
        ]);
        $forge = new Forge($token, $org)->withMockClient($mockClient);

        $servers = iterator_to_array($forge->servers()->iterate());

        expect($servers)->not->toBeEmpty()
            ->and($servers[0])->toBeInstanceOf(Server::class);
    });

test('iterate() walks across pages, forwarding cursor and filters to the next request',
    /**
     * @throws Throwable
     */
    function (): void {
        $token = ($_ENV['FORGE_TEST_TOKEN'] ?? '') ?: 'test-token';
        $org = ($_ENV['FORGE_TEST_ORG_SLUG'] ?? '') ?: 'test-org';
        $mockClient = new MockClient([
            new ServerFixture('servers/list-page-1'),
            new ServerFixture('servers/list-page-2'),
        ]);
        $forge = new Forge($token, $org)->withMockClient($mockClient);

        // Break after consuming the second page so a third (non-existent) call
        // isn't attempted — page-2's `next_cursor` redacts to a non-null
        // placeholder, so iterate() would otherwise loop again.
        $consumed = 0;
        foreach ($forge->servers()->iterate(new ListServersOptions(size: 1, sort: '-created_at')) as $server) {
            if (++$consumed === 2) {
                break;
            }
        }

        $query = $mockClient->getLastPendingRequest()?->query()->all() ?? [];

        expect($consumed)->toBe(2)
            ->and($query['page[cursor]'])->toBe('CURSOR-A')
            ->and($query['page[size]'])->toBe(1)
            ->and($query['sort'])->toBe('-created_at');
    });
