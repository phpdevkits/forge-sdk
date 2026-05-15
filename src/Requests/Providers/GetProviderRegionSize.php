<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Requests\Providers;

use Override;
use Saloon\Enums\Method;
use Saloon\Http\Request;

final class GetProviderRegionSize extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        private readonly int|string $providerId,
        private readonly int|string $regionId,
        private readonly int|string $sizeId,
    ) {}

    #[Override]
    public function resolveEndpoint(): string
    {
        return '/providers/'.$this->providerId.'/regions/'.$this->regionId.'/sizes/'.$this->sizeId;
    }
}
