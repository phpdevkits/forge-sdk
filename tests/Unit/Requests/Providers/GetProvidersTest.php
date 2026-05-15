<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Providers;

use PhpDevKits\ForgeSdk\Data\ListProvidersOptions;
use PhpDevKits\ForgeSdk\Forge;
use PhpDevKits\ForgeSdk\Requests\Providers\GetProviders;
use Saloon\Http\Faking\MockClient;
use Tests\Utils\ForgeFixture;

beforeEach(function (): void {
    $token = ($_ENV['FORGE_TEST_TOKEN'] ?? '') ?: 'test-token';

    $this->mockClient = new MockClient([
        GetProviders::class => new ForgeFixture('providers/list'),
    ]);

    $this->forge = new Forge($token)->withMockClient($this->mockClient);
});

test('sends a GET to /providers',
    /**
     * @throws Throwable
     */
    function (): void {
        $this->forge->providers()->all();

        $url = $this->mockClient->getLastPendingRequest()?->getUrl() ?? '';

        expect($url)->toBe('https://forge.laravel.com/api/providers');
    });

test('emits page[size] and page[cursor] when ListProvidersOptions is supplied',
    /**
     * @throws Throwable
     */
    function (): void {
        $this->forge->providers()->all(new ListProvidersOptions(size: 25, cursor: 'abc'));

        $query = $this->mockClient->getLastPendingRequest()?->query()->all() ?? [];

        expect($query)->toBe([
            'page[size]' => 25,
            'page[cursor]' => 'abc',
        ]);
    });
