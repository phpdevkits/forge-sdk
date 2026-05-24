<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Requests\ServerCredentials;

use Override;
use PhpDevKits\ForgeSdk\Data\ListServerCredentialsOptions;
use Saloon\Enums\Method;
use Saloon\Http\Request;

final class GetServerCredentials extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        private readonly string $organization,
        private readonly ?ListServerCredentialsOptions $options = null,
    ) {}

    #[Override]
    public function resolveEndpoint(): string
    {
        return sprintf('/orgs/%s/server-credentials', $this->organization);
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
