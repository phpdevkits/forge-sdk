<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Resources;

use PhpDevKits\ForgeSdk\Data\RedirectRule;
use PhpDevKits\ForgeSdk\Requests\Sites\DeleteRedirectRule;
use PhpDevKits\ForgeSdk\Requests\Sites\GetRedirectRule;
use Saloon\Http\BaseResource;
use Saloon\Http\Connector;
use Throwable;

final class RedirectRuleResource extends BaseResource
{
    public function __construct(
        Connector $connector,
        private readonly string $organization,
        private readonly int|string $serverId,
        private readonly int|string $siteId,
        private readonly int|string $redirectRuleId,
    ) {
        parent::__construct($connector);
    }

    /**
     * @throws Throwable
     */
    public function get(): RedirectRule
    {
        $response = $this->connector->send(new GetRedirectRule($this->organization, $this->serverId, $this->siteId, $this->redirectRuleId));

        /** @var array<array-key, mixed> $data */
        $data = $response->json('data');

        return RedirectRule::from($data);
    }

    /**
     * Remove the redirect rule from the site. Forge returns 202 Accepted with
     * an empty body and removes the rule asynchronously.
     *
     * @throws Throwable
     */
    public function delete(): void
    {
        $this->connector->send(new DeleteRedirectRule($this->organization, $this->serverId, $this->siteId, $this->redirectRuleId));
    }
}
