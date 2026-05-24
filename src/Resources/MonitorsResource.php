<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Resources;

use Generator;
use PhpDevKits\ForgeSdk\Data\CreateMonitorData;
use PhpDevKits\ForgeSdk\Data\ListMonitorsOptions;
use PhpDevKits\ForgeSdk\Data\Monitor;
use PhpDevKits\ForgeSdk\Data\Page;
use PhpDevKits\ForgeSdk\Requests\Servers\CreateMonitor;
use PhpDevKits\ForgeSdk\Requests\Servers\GetMonitors;
use PhpDevKits\ForgeSdk\Resources\Concerns\ParsesPage;
use Saloon\Http\BaseResource;
use Saloon\Http\Connector;
use Throwable;

final class MonitorsResource extends BaseResource
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
     * @return Page<Monitor>
     *
     * @throws Throwable
     */
    public function all(?ListMonitorsOptions $options = null): Page
    {
        return $this->parsePage(
            $this->connector->send(new GetMonitors($this->organization, $this->serverId, $options)),
            Monitor::from(...),
        );
    }

    /**
     * @return Generator<int, Monitor>
     *
     * @throws Throwable
     */
    public function iterate(?ListMonitorsOptions $options = null): Generator
    {
        $options ??= new ListMonitorsOptions;

        do {
            $page = $this->all($options);

            yield from $page;

            $options = new ListMonitorsOptions(
                size: $options->size,
                cursor: $page->nextCursor,
                status: $options->status,
                state: $options->state,
                type: $options->type,
                notify: $options->notify,
                sort: $options->sort,
            );
        } while ($page->hasMore());
    }

    /**
     * Add a new monitor to the server.
     *
     * Forge provisions the monitor asynchronously and returns 202 Accepted with
     * the freshly-created `MonitorResource` (its `status` will reflect the
     * pending install).
     *
     * @throws Throwable
     */
    public function create(CreateMonitorData $data): Monitor
    {
        $response = $this->connector->send(new CreateMonitor($this->organization, $this->serverId, $data));

        /** @var array<array-key, mixed> $payload */
        $payload = $response->json('data');

        return Monitor::from($payload);
    }
}
