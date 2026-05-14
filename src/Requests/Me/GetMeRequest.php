<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Requests\Me;

use Saloon\Enums\Method;
use Saloon\Http\Request;

final class GetMeRequest extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/me';
    }
}
