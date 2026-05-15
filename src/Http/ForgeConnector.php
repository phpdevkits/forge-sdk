<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Http;

use Override;
use PhpDevKits\ForgeSdk\Exceptions\ApiException;
use PhpDevKits\ForgeSdk\Exceptions\BadRequestException;
use PhpDevKits\ForgeSdk\Exceptions\ForbiddenException;
use PhpDevKits\ForgeSdk\Exceptions\NotFoundException;
use PhpDevKits\ForgeSdk\Exceptions\RateLimitException;
use PhpDevKits\ForgeSdk\Exceptions\ServerException;
use PhpDevKits\ForgeSdk\Exceptions\UnauthorizedException;
use PhpDevKits\ForgeSdk\Exceptions\ValidationException;
use Saloon\Http\Auth\TokenAuthenticator;
use Saloon\Http\Connector;
use Saloon\Http\Response;
use Saloon\Traits\Plugins\AlwaysThrowOnErrors;
use Throwable;

/**
 * @internal
 */
final class ForgeConnector extends Connector
{
    use AlwaysThrowOnErrors;

    public function __construct(private readonly string $token) {}

    public function resolveBaseUrl(): string
    {
        return 'https://forge.laravel.com/api';
    }

    #[Override]
    public function getRequestException(Response $response, ?Throwable $senderException): Throwable
    {
        $status = $response->status();

        return match (true) {
            $status === 400 => new BadRequestException($response),
            $status === 401 => new UnauthorizedException($response),
            $status === 403 => new ForbiddenException($response),
            $status === 404 => new NotFoundException($response),
            $status === 422 => new ValidationException($response),
            $status === 429 => new RateLimitException($response),
            $status >= 500 && $status < 600 => new ServerException($response),
            default => new ApiException($response),
        };
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
