<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Requests\Servers;

use Override;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * Fetch a single site. Note the server-less path: Forge exposes the
 * individual-site GET at `/orgs/{org}/sites/{site}` (the
 * `/servers/{server}/sites/{site}` path only supports PUT and DELETE).
 */
final class GetSite extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        private readonly string $organization,
        private readonly int|string $siteId,
    ) {}

    #[Override]
    public function resolveEndpoint(): string
    {
        return sprintf('/orgs/%s/sites/%s', $this->organization, $this->siteId);
    }
}
