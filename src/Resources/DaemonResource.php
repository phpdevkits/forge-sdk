<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Resources;

use PhpDevKits\ForgeSdk\Data\Daemon;
use PhpDevKits\ForgeSdk\Data\UpdateDaemonData;
use PhpDevKits\ForgeSdk\Enums\DaemonAction;
use PhpDevKits\ForgeSdk\Requests\Servers\DeleteDaemon;
use PhpDevKits\ForgeSdk\Requests\Servers\GetDaemon;
use PhpDevKits\ForgeSdk\Requests\Servers\GetDaemonLog;
use PhpDevKits\ForgeSdk\Requests\Servers\SendDaemonAction;
use PhpDevKits\ForgeSdk\Requests\Servers\UpdateDaemon;
use Saloon\Http\BaseResource;
use Saloon\Http\Connector;
use Throwable;

final class DaemonResource extends BaseResource
{
    public function __construct(
        Connector $connector,
        private readonly string $organization,
        private readonly int|string $serverId,
        private readonly int|string $daemonId,
    ) {
        parent::__construct($connector);
    }

    /**
     * @throws Throwable
     */
    public function get(): Daemon
    {
        $response = $this->connector->send(new GetDaemon($this->organization, $this->serverId, $this->daemonId));

        /** @var array<array-key, mixed> $data */
        $data = $response->json('data');

        return Daemon::from($data);
    }

    /**
     * Update the daemon. Forge returns 202 with an empty body, so this
     * returns void; call {@see get()} afterwards to read the new state.
     *
     * NOTE: the endpoint 500s when only `name` is sent — `config` (the raw
     * supervisor config) is effectively required despite the spec marking it
     * optional. Always set {@see UpdateDaemonData::$config}.
     *
     * @throws Throwable
     */
    public function update(UpdateDaemonData $data): void
    {
        $this->connector->send(new UpdateDaemon($this->organization, $this->serverId, $this->daemonId, $data));
    }

    /**
     * @throws Throwable
     */
    public function delete(): void
    {
        $this->connector->send(new DeleteDaemon($this->organization, $this->serverId, $this->daemonId));
    }

    /**
     * @throws Throwable
     */
    public function restart(): void
    {
        $this->sendAction(DaemonAction::Restart);
    }

    /**
     * @throws Throwable
     */
    public function stop(): void
    {
        $this->sendAction(DaemonAction::Stop);
    }

    /**
     * @throws Throwable
     */
    public function start(): void
    {
        $this->sendAction(DaemonAction::Start);
    }

    /**
     * @throws Throwable
     */
    public function emptyLog(): void
    {
        $this->sendAction(DaemonAction::EmptyLog);
    }

    /**
     * Return the raw supervisor log content for the daemon.
     *
     * @throws Throwable
     */
    public function log(): string
    {
        $response = $this->connector->send(new GetDaemonLog($this->organization, $this->serverId, $this->daemonId));

        $content = $response->json('data.attributes.content');

        return is_string($content) ? $content : '';
    }

    /**
     * @throws Throwable
     */
    private function sendAction(DaemonAction $action): void
    {
        $this->connector->send(new SendDaemonAction($this->organization, $this->serverId, $this->daemonId, $action));
    }
}
