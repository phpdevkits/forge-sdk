<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Requests\Servers;

use Saloon\Enums\Method;
use Saloon\Http\Request;

final class GetServerRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(private readonly int $serverId) {}

    public function resolveEndpoint(): string
    {
        return '/servers/'.$this->serverId;
    }
}
