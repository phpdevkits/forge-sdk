<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Resources;

use PhpDevKits\ForgeSdk\Data\DatabaseUser;
use PhpDevKits\ForgeSdk\Data\UpdateDatabaseUserData;
use PhpDevKits\ForgeSdk\Requests\Servers\DeleteDatabaseUser;
use PhpDevKits\ForgeSdk\Requests\Servers\GetDatabaseUser;
use PhpDevKits\ForgeSdk\Requests\Servers\UpdateDatabaseUser;
use Saloon\Http\BaseResource;
use Saloon\Http\Connector;
use Throwable;

final class DatabaseUserResource extends BaseResource
{
    public function __construct(
        Connector $connector,
        private readonly string $organization,
        private readonly int|string $serverId,
        private readonly int|string $databaseUserId,
    ) {
        parent::__construct($connector);
    }

    /**
     * @throws Throwable
     */
    public function get(): DatabaseUser
    {
        $response = $this->connector->send(new GetDatabaseUser($this->organization, $this->serverId, $this->databaseUserId));

        /** @var array<array-key, mixed> $data */
        $data = $response->json('data');

        return DatabaseUser::from($data);
    }

    /**
     * Update the database user. Forge returns 202 Accepted with an empty
     * body, so this returns void; call {@see get()} afterwards to read the
     * new state.
     *
     * @throws Throwable
     */
    public function update(UpdateDatabaseUserData $data): void
    {
        $this->connector->send(new UpdateDatabaseUser($this->organization, $this->serverId, $this->databaseUserId, $data));
    }

    /**
     * @throws Throwable
     */
    public function delete(): void
    {
        $this->connector->send(new DeleteDatabaseUser($this->organization, $this->serverId, $this->databaseUserId));
    }
}
