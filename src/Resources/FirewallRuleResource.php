<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Resources;

use PhpDevKits\ForgeSdk\Data\FirewallRule;
use PhpDevKits\ForgeSdk\Requests\Servers\DeleteFirewallRule;
use PhpDevKits\ForgeSdk\Requests\Servers\GetFirewallRule;
use Saloon\Http\BaseResource;
use Saloon\Http\Connector;
use Throwable;

final class FirewallRuleResource extends BaseResource
{
    public function __construct(
        Connector $connector,
        private readonly string $organization,
        private readonly int|string $serverId,
        private readonly int|string $ruleId,
    ) {
        parent::__construct($connector);
    }

    /**
     * @throws Throwable
     */
    public function get(): FirewallRule
    {
        $response = $this->connector->send(new GetFirewallRule($this->organization, $this->serverId, $this->ruleId));

        /** @var array<array-key, mixed> $data */
        $data = $response->json('data');

        return FirewallRule::from($data);
    }

    /**
     * @throws Throwable
     */
    public function delete(): void
    {
        $this->connector->send(new DeleteFirewallRule($this->organization, $this->serverId, $this->ruleId));
    }
}
