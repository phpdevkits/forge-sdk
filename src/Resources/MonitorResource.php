<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Resources;

use PhpDevKits\ForgeSdk\Data\Monitor;
use PhpDevKits\ForgeSdk\Requests\Servers\DeleteMonitor;
use PhpDevKits\ForgeSdk\Requests\Servers\GetMonitor;
use Saloon\Http\BaseResource;
use Saloon\Http\Connector;
use Throwable;

final class MonitorResource extends BaseResource
{
    public function __construct(
        Connector $connector,
        private readonly string $organization,
        private readonly int|string $serverId,
        private readonly int|string $monitorId,
    ) {
        parent::__construct($connector);
    }

    /**
     * @throws Throwable
     */
    public function get(): Monitor
    {
        $response = $this->connector->send(new GetMonitor($this->organization, $this->serverId, $this->monitorId));

        /** @var array<array-key, mixed> $data */
        $data = $response->json('data');

        return Monitor::from($data);
    }

    /**
     * @throws Throwable
     */
    public function delete(): void
    {
        $this->connector->send(new DeleteMonitor($this->organization, $this->serverId, $this->monitorId));
    }
}
