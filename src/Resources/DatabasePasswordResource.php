<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Resources;

use PhpDevKits\ForgeSdk\Data\UpdateDatabasePasswordData;
use PhpDevKits\ForgeSdk\Requests\Servers\UpdateDatabasePassword;
use Saloon\Http\BaseResource;
use Saloon\Http\Connector;
use Throwable;

final class DatabasePasswordResource extends BaseResource
{
    public function __construct(
        Connector $connector,
        private readonly string $organization,
        private readonly int|string $serverId,
    ) {
        parent::__construct($connector);
    }

    /**
     * Update the password Forge stores for the server's database.
     *
     * Per the Forge docs this is a sync operation that returns 204 No
     * Content, so this method returns void. It does NOT change the
     * password on the database server itself — use it only to bring
     * Forge's stored copy in line with an out-of-band rotation.
     *
     * @throws Throwable
     */
    public function update(UpdateDatabasePasswordData $data): void
    {
        $this->connector->send(
            new UpdateDatabasePassword($this->organization, $this->serverId, $data),
        );
    }
}
