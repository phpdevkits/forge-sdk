<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Requests\Servers;

use Override;
use PhpDevKits\ForgeSdk\Data\ListServersOptions;
use Saloon\Enums\Method;
use Saloon\Http\Request;

final class GetServers extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        private readonly string $organization,
        private readonly ?ListServersOptions $options = null,
    ) {}

    #[Override]
    public function resolveEndpoint(): string
    {
        return sprintf('/orgs/%s/servers', $this->organization);
    }

    /**
     * @return array<string, int|string>
     */
    #[Override]
    protected function defaultQuery(): array
    {
        return $this->options?->toQuery() ?? [];
    }
}
