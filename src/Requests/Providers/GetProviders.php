<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Requests\Providers;

use Override;
use PhpDevKits\ForgeSdk\Data\ListProvidersOptions;
use Saloon\Enums\Method;
use Saloon\Http\Request;

final class GetProviders extends Request
{
    protected Method $method = Method::GET;

    public function __construct(private readonly ?ListProvidersOptions $options = null) {}

    #[Override]
    public function resolveEndpoint(): string
    {
        return '/providers';
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
