<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Http;

use Saloon\Http\Auth\TokenAuthenticator;
use Saloon\Http\Connector;

/**
 * @internal
 */
final class ForgeConnector extends Connector
{
    public function __construct(private readonly string $token) {}

    public function resolveBaseUrl(): string
    {
        return 'https://forge.laravel.com/api';
    }

    protected function defaultAuth(): TokenAuthenticator
    {
        return new TokenAuthenticator($this->token);
    }

    /**
     * @return array<string, string>
     */
    protected function defaultHeaders(): array
    {
        return [
            'Accept' => 'application/vnd.api+json',
            'Content-Type' => 'application/vnd.api+json',
        ];
    }
}
