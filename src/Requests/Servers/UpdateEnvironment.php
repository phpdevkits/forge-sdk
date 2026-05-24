<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Requests\Servers;

use Override;
use PhpDevKits\ForgeSdk\Data\UpdateEnvironmentData;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

final class UpdateEnvironment extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PUT;

    public function __construct(
        private readonly string $organization,
        private readonly int|string $serverId,
        private readonly int|string $siteId,
        private readonly UpdateEnvironmentData $data,
    ) {}

    #[Override]
    public function resolveEndpoint(): string
    {
        return sprintf('/orgs/%s/servers/%s/sites/%s/environment', $this->organization, $this->serverId, $this->siteId);
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultBody(): array
    {
        return $this->data->toArray();
    }
}
