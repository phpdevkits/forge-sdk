<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Resources;

use Generator;
use PhpDevKits\ForgeSdk\Data\CreateDatabaseData;
use PhpDevKits\ForgeSdk\Data\Database;
use PhpDevKits\ForgeSdk\Data\ListDatabasesOptions;
use PhpDevKits\ForgeSdk\Data\Page;
use PhpDevKits\ForgeSdk\Requests\Servers\CreateDatabase;
use PhpDevKits\ForgeSdk\Requests\Servers\GetDatabases;
use PhpDevKits\ForgeSdk\Requests\Servers\SyncDatabases;
use PhpDevKits\ForgeSdk\Resources\Concerns\ParsesPage;
use Saloon\Http\BaseResource;
use Saloon\Http\Connector;
use Throwable;

final class DatabasesResource extends BaseResource
{
    use ParsesPage;

    public function __construct(
        Connector $connector,
        private readonly string $organization,
        private readonly int|string $serverId,
    ) {
        parent::__construct($connector);
    }

    /**
     * @return Page<Database>
     *
     * @throws Throwable
     */
    public function all(?ListDatabasesOptions $options = null): Page
    {
        return $this->parsePage(
            $this->connector->send(new GetDatabases($this->organization, $this->serverId, $options)),
            Database::from(...),
        );
    }

    /**
     * @return Generator<int, Database>
     *
     * @throws Throwable
     */
    public function iterate(?ListDatabasesOptions $options = null): Generator
    {
        $options ??= new ListDatabasesOptions;

        do {
            $page = $this->all($options);

            yield from $page;

            $options = new ListDatabasesOptions(
                size: $options->size,
                cursor: $page->nextCursor,
                sort: $options->sort,
                name: $options->name,
                status: $options->status,
            );
        } while ($page->hasMore());
    }

    /**
     * Create a new database schema on the server.
     *
     * Forge processes the install asynchronously and returns 202 Accepted with
     * the freshly-created `DatabaseResource` (status `installing`). The returned
     * Database reflects that initial state; poll {@see all()} / {@see iterate()}
     * to observe the transition to `installed`.
     *
     * @throws Throwable
     */
    public function create(CreateDatabaseData $data): Database
    {
        $response = $this->connector->send(new CreateDatabase($this->organization, $this->serverId, $data));

        /** @var array<array-key, mixed> $payload */
        $payload = $response->json('data');

        return Database::from($payload);
    }

    /**
     * Synchronize all database schemas on the server.
     *
     * Fires an async reconciliation and returns 202 Accepted with an empty body.
     *
     * @throws Throwable
     */
    public function sync(): void
    {
        $this->connector->send(new SyncDatabases($this->organization, $this->serverId));
    }
}
