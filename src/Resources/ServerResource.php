<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Resources;

use PhpDevKits\ForgeSdk\Data\Server;
use PhpDevKits\ForgeSdk\Data\UpdateServerData;
use PhpDevKits\ForgeSdk\Enums\ServerAction;
use PhpDevKits\ForgeSdk\Requests\Servers\DeleteServer;
use PhpDevKits\ForgeSdk\Requests\Servers\GetServer;
use PhpDevKits\ForgeSdk\Requests\Servers\SendServerAction;
use PhpDevKits\ForgeSdk\Requests\Servers\UpdateServer;
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

    /**
     * @throws Throwable
     */
    public function update(UpdateServerData $data): Server
    {
        $response = $this->connector->send(new UpdateServer($this->organization, $this->id, $data));

        /** @var array<array-key, mixed> $body */
        $body = $response->json('data');

        return Server::from($body);
    }

    /**
     * @throws Throwable
     */
    public function delete(): void
    {
        $this->connector->send(new DeleteServer($this->organization, $this->id));
    }

    /**
     * @throws Throwable
     */
    public function reboot(): void
    {
        $this->sendAction(ServerAction::Reboot);
    }

    /**
     * @throws Throwable
     */
    public function powerCycle(): void
    {
        $this->sendAction(ServerAction::PowerCycle);
    }

    /**
     * @throws Throwable
     */
    private function sendAction(ServerAction $action): void
    {
        $this->connector->send(new SendServerAction($this->organization, $this->id, $action));
    }
}
