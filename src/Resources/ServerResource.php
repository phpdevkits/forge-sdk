<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Resources;

use PhpDevKits\ForgeSdk\Data\Server;
use PhpDevKits\ForgeSdk\Requests\Servers\GetServer;
use Saloon\Http\BaseResource;
use Saloon\Http\Connector;
use Throwable;

final class ServerResource extends BaseResource
{
    public function __construct(
        Connector $connector,
        private readonly string $organization,
        private readonly int|string $id,
    ) {
        parent::__construct($connector);
    }

    /**
     * @throws Throwable
     */
    public function get(): Server
    {
        $response = $this->connector->send(new GetServer($this->organization, $this->id));

        /** @var array<array-key, mixed> $data */
        $data = $response->json('data');

        return Server::from($data);
    }
}
