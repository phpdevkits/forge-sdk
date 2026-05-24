<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Requests\Sites;

use Override;
use PhpDevKits\ForgeSdk\Data\CreateRedirectRuleData;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

final class CreateRedirectRule extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        private readonly string $organization,
        private readonly int|string $serverId,
        private readonly int|string $siteId,
        private readonly CreateRedirectRuleData $data,
    ) {}

    #[Override]
    public function resolveEndpoint(): string
    {
        return sprintf('/orgs/%s/servers/%s/sites/%s/redirect-rules', $this->organization, $this->serverId, $this->siteId);
    }

    /**
     * @return array<string, string>
     */
    protected function defaultBody(): array
    {
        return $this->data->toArray();
    }
}
