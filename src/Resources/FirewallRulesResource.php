<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Resources;

use Generator;
use PhpDevKits\ForgeSdk\Data\CreateFirewallRuleData;
use PhpDevKits\ForgeSdk\Data\FirewallRule;
use PhpDevKits\ForgeSdk\Data\ListFirewallRulesOptions;
use PhpDevKits\ForgeSdk\Data\Page;
use PhpDevKits\ForgeSdk\Requests\Servers\CreateFirewallRule;
use PhpDevKits\ForgeSdk\Requests\Servers\GetFirewallRules;
use PhpDevKits\ForgeSdk\Resources\Concerns\ParsesPage;
use Saloon\Http\BaseResource;
use Saloon\Http\Connector;
use Throwable;

// NOTE: POST /firewall-rules returns 202 Accepted with an empty body — Forge
// installs the rule asynchronously on the server. The endpoint does not return
// the newly-created RuleResource. Callers wanting the created FirewallRule DTO
// should call $this->all() / $this->iterate() after create and match by name.

final class FirewallRulesResource extends BaseResource
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
     * @return Page<FirewallRule>
     *
     * @throws Throwable
     */
    public function all(?ListFirewallRulesOptions $options = null): Page
    {
        return $this->parsePage(
            $this->connector->send(new GetFirewallRules($this->organization, $this->serverId, $options)),
            FirewallRule::from(...),
        );
    }

    /**
     * @return Generator<int, FirewallRule>
     *
     * @throws Throwable
     */
    public function iterate(?ListFirewallRulesOptions $options = null): Generator
    {
        $options ??= new ListFirewallRulesOptions;

        do {
            $page = $this->all($options);

            yield from $page;

            $options = new ListFirewallRulesOptions(
                size: $options->size,
                cursor: $page->nextCursor,
                sort: $options->sort,
                name: $options->name,
                status: $options->status,
                ipAddress: $options->ipAddress,
                type: $options->type,
                port: $options->port,
            );
        } while ($page->hasMore());
    }

    /**
     * Add a new firewall rule to the server.
     *
     * Forge fires the install asynchronously and returns 202 Accepted with an
     * empty body — there's no `RuleResource` to hydrate. To retrieve the
     * created rule, call {@see all()} / {@see iterate()} afterwards and match
     * by name.
     *
     * @throws Throwable
     */
    public function create(CreateFirewallRuleData $data): void
    {
        $this->connector->send(new CreateFirewallRule($this->organization, $this->serverId, $data));
    }
}
