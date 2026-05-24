<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use PhpDevKits\ForgeSdk\Data\Database;
use PhpDevKits\ForgeSdk\Data\Page;
use PhpDevKits\ForgeSdk\Forge;
use PhpDevKits\ForgeSdk\Requests\Servers\GetDatabases;
use Saloon\Http\Faking\MockClient;
use Tests\Utils\DatabaseFixture;

test('all() returns a Page<Database>',
    /**
     * @throws Throwable
     */
    function (): void {
        $token = ($_ENV['FORGE_TEST_TOKEN'] ?? '') ?: 'test-token';
        $org = ($_ENV['FORGE_TEST_ORG_SLUG'] ?? '') ?: 'test-org';
        $serverId = ($_ENV['FORGE_TEST_DATABASE_SERVER_ID'] ?? '') ?: '1';
        $mockClient = new MockClient([
            GetDatabases::class => new DatabaseFixture('databases/list'),
        ]);
        $forge = new Forge($token, $org)->withMockClient($mockClient);

        $page = $forge->server($serverId)->databases()->all();

        expect($page)->toBeInstanceOf(Page::class)
            ->and($page->data)->not->toBeEmpty()
            ->and($page->data[0])->toBeInstanceOf(Database::class);
    });

test('iterate() yields each database',
    /**
     * @throws Throwable
     */
    function (): void {
        $token = ($_ENV['FORGE_TEST_TOKEN'] ?? '') ?: 'test-token';
        $org = ($_ENV['FORGE_TEST_ORG_SLUG'] ?? '') ?: 'test-org';
        $serverId = ($_ENV['FORGE_TEST_DATABASE_SERVER_ID'] ?? '') ?: '1';
        $mockClient = new MockClient([
            GetDatabases::class => new DatabaseFixture('databases/list'),
        ]);
        $forge = new Forge($token, $org)->withMockClient($mockClient);

        $databases = iterator_to_array($forge->server($serverId)->databases()->iterate());

        expect($databases)->not->toBeEmpty()
            ->and($databases[0])->toBeInstanceOf(Database::class);
    });
