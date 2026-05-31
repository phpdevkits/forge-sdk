<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Resources;

use Generator;
use PhpDevKits\ForgeSdk\Data\CreateDatabaseUserData;
use PhpDevKits\ForgeSdk\Data\DatabaseUser;
use PhpDevKits\ForgeSdk\Data\ListDatabaseUsersOptions;
use PhpDevKits\ForgeSdk\Data\Page;
use PhpDevKits\ForgeSdk\Requests\Servers\CreateDatabaseUser;
use PhpDevKits\ForgeSdk\Requests\Servers\GetDatabaseUsers;
use PhpDevKits\ForgeSdk\Resources\Concerns\ParsesPage;
use Saloon\Http\BaseResource;
use Saloon\Http\Connector;
use Throwable;

final class DatabaseUsersResource extends BaseResource
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
     * @return Page<DatabaseUser>
     *
     * @throws Throwable
     */
    public function all(?ListDatabaseUsersOptions $options = null): Page
    {
        return $this->parsePage(
            $this->connector->send(new GetDatabaseUsers($this->organization, $this->serverId, $options)),
            DatabaseUser::from(...),
        );
    }

    /**
     * @return Generator<int, DatabaseUser>
     *
     * @throws Throwable
     */
    public function iterate(?ListDatabaseUsersOptions $options = null): Generator
    {
        $options ??= new ListDatabaseUsersOptions;

        do {
            $page = $this->all($options);

            yield from $page;

            $options = new ListDatabaseUsersOptions(
                size: $options->size,
                cursor: $page->nextCursor,
                sort: $options->sort,
                name: $options->name,
                status: $options->status,
            );
        } while ($page->hasMore());
    }

    /**
     * Create a new database user on the server.
     *
     * Forge processes the install asynchronously and returns 202 Accepted
     * with the freshly-created `DatabaseUserResource` (status `installing`).
     * The returned DatabaseUser reflects that initial state; poll
     * {@see all()} / {@see iterate()} to observe the transition to
     * `installed`.
     *
     * @throws Throwable
     */
    public function create(CreateDatabaseUserData $data): DatabaseUser
    {
        $response = $this->connector->send(new CreateDatabaseUser($this->organization, $this->serverId, $data));

        /** @var array<array-key, mixed> $payload */
        $payload = $response->json('data');

        return DatabaseUser::from($payload);
    }
}
