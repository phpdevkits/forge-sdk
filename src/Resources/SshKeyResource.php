<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Resources;

use PhpDevKits\ForgeSdk\Data\SshKey;
use PhpDevKits\ForgeSdk\Requests\Servers\DeleteSshKey;
use PhpDevKits\ForgeSdk\Requests\Servers\GetSshKey;
use Saloon\Http\BaseResource;
use Saloon\Http\Connector;
use Throwable;

final class SshKeyResource extends BaseResource
{
    public function __construct(
        Connector $connector,
        private readonly string $organization,
        private readonly int|string $serverId,
        private readonly int|string $keyId,
    ) {
        parent::__construct($connector);
    }

    /**
     * @throws Throwable
     */
    public function get(): SshKey
    {
        $response = $this->connector->send(new GetSshKey($this->organization, $this->serverId, $this->keyId));

        /** @var array<array-key, mixed> $data */
        $data = $response->json('data');

        return SshKey::from($data);
    }

    /**
     * @throws Throwable
     */
    public function delete(): void
    {
        $this->connector->send(new DeleteSshKey($this->organization, $this->serverId, $this->keyId));
    }
}
