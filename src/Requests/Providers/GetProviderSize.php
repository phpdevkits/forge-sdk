<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Requests\Providers;

use Override;
use Saloon\Enums\Method;
use Saloon\Http\Request;

final class GetProviderSize extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        private readonly int|string $providerId,
        private readonly int|string $sizeId,
    ) {}

    #[Override]
    public function resolveEndpoint(): string
    {
        return '/providers/'.$this->providerId.'/sizes/'.$this->sizeId;
    }
}
