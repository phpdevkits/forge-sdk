<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Requests\Providers;

use Override;
use PhpDevKits\ForgeSdk\Data\ListProviderSizesOptions;
use Saloon\Enums\Method;
use Saloon\Http\Request;

final class GetProviderSizes extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        private readonly int|string $providerId,
        private readonly ?ListProviderSizesOptions $options = null,
    ) {}

    #[Override]
    public function resolveEndpoint(): string
    {
        return '/providers/'.$this->providerId.'/sizes';
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
