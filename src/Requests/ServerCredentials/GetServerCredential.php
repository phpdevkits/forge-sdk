<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Requests\ServerCredentials;

use Override;
use Saloon\Enums\Method;
use Saloon\Http\Request;

final class GetServerCredential extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        private readonly string $organization,
        private readonly int|string $credentialId,
    ) {}

    #[Override]
    public function resolveEndpoint(): string
    {
        return sprintf('/orgs/%s/server-credentials/%s', $this->organization, $this->credentialId);
    }
}
