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

    public function sshKeys(): SshKeysResource
    {
        return new SshKeysResource($this->connector, $this->organization, $this->id);
    }

    public function sshKey(int|string $keyId): SshKeyResource
    {
        return new SshKeyResource($this->connector, $this->organization, $this->id, $keyId);
    }

    public function sites(): SitesResource
    {
        return new SitesResource($this->connector, $this->organization, $this->id);
    }

    public function site(int|string $siteId): SiteResource
    {
        return new SiteResource($this->connector, $this->organization, $this->id, $siteId);
    }

    public function daemons(): DaemonsResource
    {
        return new DaemonsResource($this->connector, $this->organization, $this->id);
    }

    public function daemon(int|string $daemonId): DaemonResource
    {
        return new DaemonResource($this->connector, $this->organization, $this->id, $daemonId);
    }

    public function databases(): DatabasesResource
    {
        return new DatabasesResource($this->connector, $this->organization, $this->id);
    }

    public function database(int|string $databaseId): DatabaseResource
    {
        return new DatabaseResource($this->connector, $this->organization, $this->id, $databaseId);
    }

    public function firewallRules(): FirewallRulesResource
    {
        return new FirewallRulesResource($this->connector, $this->organization, $this->id);
    }

    public function firewallRule(int|string $ruleId): FirewallRuleResource
    {
        return new FirewallRuleResource($this->connector, $this->organization, $this->id, $ruleId);
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
