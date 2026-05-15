<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Servers;

use PhpDevKits\ForgeSdk\Data\ListServersOptions;
use PhpDevKits\ForgeSdk\Forge;
use PhpDevKits\ForgeSdk\Requests\Servers\GetServers;
use Saloon\Http\Faking\MockClient;
use Tests\Utils\ForgeFixture;

beforeEach(function (): void {
    $token = ($_ENV['FORGE_TEST_TOKEN'] ?? '') ?: 'test-token';
    $org = ($_ENV['FORGE_TEST_ORG_SLUG'] ?? '') ?: 'test-org';

    $this->mockClient = new MockClient([
        GetServers::class => new ForgeFixture('servers/list'),
    ]);

    $this->forge = new Forge($token, $org)->withMockClient($this->mockClient);
    $this->org = $org;
});

test('sends a GET to /orgs/{org}/servers',
    /**
     * @throws Throwable
     */
    function (): void {
        $this->forge->servers()->all();

        $url = $this->mockClient->getLastPendingRequest()?->getUrl() ?? '';

        expect($url)->toBe(sprintf('https://forge.laravel.com/api/orgs/%s/servers', $this->org));
    });

test('emits pagination + sort + filter query params when ListServersOptions is supplied',
    /**
     * @throws Throwable
     */
    function (): void {
        $this->forge->servers()->all(new ListServersOptions(
            size: 10,
            cursor: 'CURSOR-Z',
            sort: '-created_at',
            provider: 'aws',
            phpVersion: 'php84',
        ));

        $query = $this->mockClient->getLastPendingRequest()?->query()->all() ?? [];

        expect($query)->toBe([
            'page[size]' => 10,
            'page[cursor]' => 'CURSOR-Z',
            'sort' => '-created_at',
            'filter[provider]' => 'aws',
            'filter[php_version]' => 'php84',
        ]);
    });

test('resolveEndpoint() interpolates the organization slug',
    function (): void {
        $request = new GetServers('chained-org');

        expect($request->resolveEndpoint())->toBe('/orgs/chained-org/servers');
    });
