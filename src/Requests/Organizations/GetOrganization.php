<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Requests\Organizations;

use Override;
use Saloon\Enums\Method;
use Saloon\Http\Request;

final class GetOrganization extends Request
{
    protected Method $method = Method::GET;

    public function __construct(private readonly string $slug) {}

    #[Override]
    public function resolveEndpoint(): string
    {
        return '/orgs/'.$this->slug;
    }
}
