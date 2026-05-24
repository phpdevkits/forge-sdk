<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use PhpDevKits\ForgeSdk\Data\CreateDatabaseData;
use PhpDevKits\ForgeSdk\Data\Database;
use PhpDevKits\ForgeSdk\Forge;
use PhpDevKits\ForgeSdk\Requests\Servers\CreateDatabase;
use PhpDevKits\ForgeSdk\Requests\Servers\DeleteDatabase;
use PhpDevKits\ForgeSdk\Requests\Servers\GetDatabase;
use PhpDevKits\ForgeSdk\Requests\Servers\GetDatabases;
use PhpDevKits\ForgeSdk\Requests\Servers\SyncDatabases;
use Saloon\Http\Faking\MockClient;
use Tests\Utils\DatabaseFixture;
use Throwable;

/*
 * End-to-end database-schema lifecycle: create -> list -> get -> sync ->
 * delete, against the configured Forge server (recording) or replayed from
 * committed fixtures otherwise. try/finally guards cleanup so a partway
 * failure during recording doesn't leak a real schema.
 *
 * Required env vars (recording only): FORGE_TEST_TOKEN, FORGE_TEST_ORG_SLUG,
 * FORGE_TEST_SERVER_ID (a server with a database engine installed).
 */
test('lifecycle: create -> list -> get -> sync -> delete',
    /**
     * @throws Throwable
     */
    function (): void {
        $token = ($_ENV['FORGE_TEST_TOKEN'] ?? '') ?: 'test-token';
        $org = ($_ENV['FORGE_TEST_ORG_SLUG'] ?? '') ?: 'test-org';
        $serverId = ($_ENV['FORGE_TEST_DATABASE_SERVER_ID'] ?? '') ?: '1';

        $mockClient = new MockClient([
            CreateDatabase::class => new DatabaseFixture('databases/create'),
            GetDatabases::class => new DatabaseFixture('databases/list'),
            GetDatabase::class => new DatabaseFixture('databases/get'),
            SyncDatabases::class => new DatabaseFixture('databases/sync'),
            DeleteDatabase::class => new DatabaseFixture('databases/delete'),
        ]);
        $forge = new Forge($token, $org)->withMockClient($mockClient);

        $createdId = null;
        try {
            $created = $forge->server($serverId)->databases()->create(
                new CreateDatabaseData(name: 'sdk_test_db'),
            );
            $createdId = $created->id;

            expect($created)->toBeInstanceOf(Database::class)
                ->and($created->id)->toBeString()->not->toBeEmpty();

            $page = $forge->server($serverId)->databases()->all();
            expect($page->data)->not->toBeEmpty();

            $fetched = $forge->server($serverId)->database($createdId)->get();
            expect($fetched)->toBeInstanceOf(Database::class);

            $forge->server($serverId)->databases()->sync();

            $forge->server($serverId)->database($createdId)->delete();
            $createdId = null;
        } finally {
            if ($createdId !== null) {
                try {
                    $forge->server($serverId)->database($createdId)->delete();
                } catch (Throwable) {
                    // best-effort cleanup; original failure surfaces
                }
            }
        }
    });
