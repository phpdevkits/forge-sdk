<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Organizations;

use PhpDevKits\ForgeSdk\Data\ListOrganizationsOptions;
use PhpDevKits\ForgeSdk\Forge;
use PhpDevKits\ForgeSdk\Requests\Organizations\GetOrganizations;
use Saloon\Http\Faking\MockClient;
use Tests\Utils\ForgeFixture;

beforeEach(function (): void {
    $token = ($_ENV['FORGE_TEST_TOKEN'] ?? '') ?: 'test-token';

    $this->mockClient = new MockClient([
        GetOrganizations::class => new ForgeFixture('organizations/list'),
    ]);

    $this->forge = new Forge($token)->withMockClient($this->mockClient);
});

test('sends a GET to /orgs',
    /**
     * @throws Throwable
     */
    function (): void {
        $this->forge->organizations()->all();

        $url = $this->mockClient->getLastPendingRequest()?->getUrl() ?? '';

        expect($url)->toStartWith('https://forge.laravel.com/api/orgs');
    });

test('sends no query params when called with no options',
    /**
     * @throws Throwable
     */
    function (): void {
        $this->forge->organizations()->all();

        $url = $this->mockClient->getLastPendingRequest()?->getUrl() ?? '';

        expect($url)->toBe('https://forge.laravel.com/api/orgs');
    });

test('emits page[size] and page[cursor] when ListOrganizationsOptions is supplied',
    /**
     * @throws Throwable
     */
    function (): void {
        $this->forge->organizations()->all(new ListOrganizationsOptions(size: 25, cursor: 'abc'));

        $query = $this->mockClient->getLastPendingRequest()?->query()->all() ?? [];

        expect($query)->toBe([
            'page[size]' => 25,
            'page[cursor]' => 'abc',
        ]);
    });
