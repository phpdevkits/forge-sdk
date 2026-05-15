<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Requests\Organizations;

use Override;
use PhpDevKits\ForgeSdk\Data\ListOrganizationsOptions;
use Saloon\Enums\Method;
use Saloon\Http\Request;

final class GetOrganizations extends Request
{
    protected Method $method = Method::GET;

    public function __construct(private readonly ?ListOrganizationsOptions $options = null) {}

    #[Override]
    public function resolveEndpoint(): string
    {
        return '/orgs';
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
