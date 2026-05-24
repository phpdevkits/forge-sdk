<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Requests\ServerCredentials;

use Override;
use PhpDevKits\ForgeSdk\Data\CreateVpcData;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

final class CreateVpc extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        private readonly string $organization,
        private readonly int|string $credentialId,
        private readonly string $region,
        private readonly CreateVpcData $data,
    ) {}

    #[Override]
    public function resolveEndpoint(): string
    {
        return sprintf('/orgs/%s/server-credentials/%s/regions/%s/vpcs', $this->organization, $this->credentialId, $this->region);
    }

    /**
     * @return array<string, string>
     */
    protected function defaultBody(): array
    {
        return $this->data->toArray();
    }
}
