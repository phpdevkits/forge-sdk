<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Requests\Providers;

use Override;
use PhpDevKits\ForgeSdk\Data\ListProviderRegionSizesOptions;
use Saloon\Enums\Method;
use Saloon\Http\Request;

final class GetProviderRegionSizes extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        private readonly int|string $providerId,
        private readonly int|string $regionId,
        private readonly ?ListProviderRegionSizesOptions $options = null,
    ) {}

    #[Override]
    public function resolveEndpoint(): string
    {
        return '/providers/'.$this->providerId.'/regions/'.$this->regionId.'/sizes';
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
