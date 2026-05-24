<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Resources;

use PhpDevKits\ForgeSdk\Data\Database;
use PhpDevKits\ForgeSdk\Requests\Servers\DeleteDatabase;
use PhpDevKits\ForgeSdk\Requests\Servers\GetDatabase;
use Saloon\Http\BaseResource;
use Saloon\Http\Connector;
use Throwable;

final class DatabaseResource extends BaseResource
{
    public function __construct(
        Connector $connector,
        private readonly string $organization,
        private readonly int|string $serverId,
        private readonly int|string $databaseId,
    ) {
        parent::__construct($connector);
    }

    /**
     * @throws Throwable
     */
    public function get(): Database
    {
        $response = $this->connector->send(new GetDatabase($this->organization, $this->serverId, $this->databaseId));

        /** @var array<array-key, mixed> $data */
        $data = $response->json('data');

        return Database::from($data);
    }

    /**
     * @throws Throwable
     */
    public function delete(): void
    {
        $this->connector->send(new DeleteDatabase($this->organization, $this->serverId, $this->databaseId));
    }
}
