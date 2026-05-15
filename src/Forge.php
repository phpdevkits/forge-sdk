<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk;

use InvalidArgumentException;
use JsonException;
use Override;
use PhpDevKits\ForgeSdk\Data\User;
use PhpDevKits\ForgeSdk\Exceptions\ApiException;
use PhpDevKits\ForgeSdk\Exceptions\BadRequestException;
use PhpDevKits\ForgeSdk\Exceptions\ConnectionException;
use PhpDevKits\ForgeSdk\Exceptions\ForbiddenException;
use PhpDevKits\ForgeSdk\Exceptions\NotFoundException;
use PhpDevKits\ForgeSdk\Exceptions\OrganizationNotSetException;
use PhpDevKits\ForgeSdk\Exceptions\RateLimitException;
use PhpDevKits\ForgeSdk\Exceptions\ServerException;
use PhpDevKits\ForgeSdk\Exceptions\UnauthorizedException;
use PhpDevKits\ForgeSdk\Exceptions\ValidationException;
use PhpDevKits\ForgeSdk\Requests\Me\GetMe;
use PhpDevKits\ForgeSdk\Resources\OrganizationResource;
use PhpDevKits\ForgeSdk\Resources\OrganizationsResource;
use PhpDevKits\ForgeSdk\Resources\ProviderResource;
use PhpDevKits\ForgeSdk\Resources\ProvidersResource;
use PhpDevKits\ForgeSdk\Resources\ServerResource;
use PhpDevKits\ForgeSdk\Resources\ServersResource;
use RuntimeException;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Http\Auth\TokenAuthenticator;
use Saloon\Http\Connector;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Plugins\AlwaysThrowOnErrors;
use Throwable;

final class Forge extends Connector
{
    use AlwaysThrowOnErrors;

    public function __construct(private readonly string $token, public readonly ?string $defaultOrganization = null) {}

    public static function fromConfig(?string $path = null): self
    {
        if ($path === null) {
            $envPath = $_ENV['FORGE_CONFIG_PATH'] ?? null;
            $path = is_string($envPath) && $envPath !== '' ? $envPath : getcwd().'/forge.json';
        }

        if (! is_file($path)) {
            throw new InvalidArgumentException(sprintf('Forge config file not found at "%s".', $path));
        }

        $contents = (string) file_get_contents($path);

        try {
            /** @var mixed $decoded */
            $decoded = json_decode($contents, associative: true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException $jsonException) {
            throw new InvalidArgumentException(sprintf('Forge config file at "%s" is not valid JSON.', $path), $jsonException->getCode(), previous: $jsonException);
        }

        if (! is_array($decoded)) {
            throw new InvalidArgumentException(sprintf('Forge config file at "%s" must contain a JSON object.', $path));
        }

        $token = $decoded['token'] ?? null;

        if (! is_string($token) || $token === '') {
            throw new InvalidArgumentException(sprintf('Forge config file at "%s" is missing the required `token` key.', $path));
        }

        $organization = $decoded['organization'] ?? null;

        if (! is_string($organization) || $organization === '') {
            $organization = null;
        }

        return new self($token, $organization);
    }

    public static function fromEnvironment(): self
    {
        $token = $_ENV['FORGE_TOKEN'] ?? null;

        if (! is_string($token) || $token === '') {
            throw new InvalidArgumentException('FORGE_TOKEN environment variable is required.');
        }

        $organization = $_ENV['FORGE_ORGANIZATION'] ?? null;

        if (! is_string($organization) || $organization === '') {
            $organization = null;
        }

        return new self($token, $organization);
    }

    public function resolveBaseUrl(): string
    {
        return 'https://forge.laravel.com/api';
    }

    #[Override]
    public function getRequestException(Response $response, ?Throwable $senderException): Throwable
    {
        $status = $response->status();

        if ($status === 400) {
            return new BadRequestException($response);
        }

        if ($status === 401) {
            return new UnauthorizedException($response);
        }

        if ($status === 403) {
            return new ForbiddenException($response);
        }

        if ($status === 404) {
            return new NotFoundException($response);
        }

        if ($status === 422) {
            return new ValidationException($response);
        }

        if ($status === 429) {
            return new RateLimitException($response);
        }

        if ($status >= 500 && $status < 600) {
            return new ServerException($response);
        }

        return new ApiException($response);
    }

    public function me(): User
    {
        $response = $this->send(new GetMe);

        $data = $response->json('data');

        if (! is_array($data)) {
            throw new RuntimeException('Forge /me response did not include a `data` object.');
        }

        return User::from($data);
    }

    public function organizations(): OrganizationsResource
    {
        return new OrganizationsResource($this);
    }

    public function organization(string $slug): OrganizationResource
    {
        return new OrganizationResource($this, $slug);
    }

    public function providers(): ProvidersResource
    {
        return new ProvidersResource($this);
    }

    public function provider(int|string $id): ProviderResource
    {
        return new ProviderResource($this, $id);
    }

    /**
     * Return a clone of this connector bound to the given organization slug,
     * overriding any default org from the constructor / env / config.
     *
     * The original connector is left untouched — chains stay immutable so
     * concurrent callers can scope themselves without stepping on each other.
     * Connector-level state (notably the `MockClient` used by tests) is
     * carried over so chained calls don't escape the mock and hit the real
     * API.
     */
    public function org(string $slug): self
    {
        $clone = new self($this->token, $slug);

        $mockClient = $this->getMockClient();
        if ($mockClient instanceof MockClient) {
            $clone->withMockClient($mockClient);
        }

        return $clone;
    }

    public function servers(): ServersResource
    {
        return new ServersResource($this, $this->requireOrganization('servers'));
    }

    public function server(int|string $id): ServerResource
    {
        return new ServerResource($this, $this->requireOrganization('server'), $id);
    }

    private function requireOrganization(string $accessor): string
    {
        if ($this->defaultOrganization === null) {
            throw OrganizationNotSetException::forAccessor($accessor);
        }

        return $this->defaultOrganization;
    }

    #[Override]
    public function send(
        Request $request,
        ?MockClient $mockClient = null,
        ?callable $handleRetry = null,
    ): Response {
        try {
            return parent::send($request, $mockClient, $handleRetry);
        } catch (FatalRequestException $fatalRequestException) {
            throw new ConnectionException(
                'Forge API connection failed: '.$fatalRequestException->getMessage(),
                $fatalRequestException,
            );
        }
    }

    #[Override]
    protected function defaultAuth(): TokenAuthenticator
    {
        return new TokenAuthenticator($this->token);
    }

    /**
     * @return array<string, string>
     */
    #[Override]
    protected function defaultHeaders(): array
    {
        return [
            'Accept' => 'application/vnd.api+json',
            'Content-Type' => 'application/vnd.api+json',
        ];
    }
}
