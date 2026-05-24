<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Requests\ServerCredentials;

use Override;
use Saloon\Enums\Method;
use Saloon\Http\Request;

final class GetVpcs extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        private readonly string $organization,
        private readonly int|string $credentialId,
        private readonly string $region,
    ) {}

    #[Override]
    public function resolveEndpoint(): string
    {
        return sprintf('/orgs/%s/server-credentials/%s/regions/%s/vpcs', $this->organization, $this->credentialId, $this->region);
    }
}
