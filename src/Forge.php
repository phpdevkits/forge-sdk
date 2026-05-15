<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk;

use InvalidArgumentException;
use JsonException;
use PhpDevKits\ForgeSdk\Data\User;
use PhpDevKits\ForgeSdk\Http\ForgeConnector;
use PhpDevKits\ForgeSdk\Requests\Me\GetMeRequest;
use RuntimeException;
use Saloon\Http\Faking\MockClient;

final readonly class Forge
{
    private ForgeConnector $connector;

    public function __construct(string $token, public ?string $organization = null)
    {
        $this->connector = new ForgeConnector($token);
    }

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

    public function withMockClient(MockClient $mockClient): self
    {
        $this->connector->withMockClient($mockClient);

        return $this;
    }

    public function me(): User
    {
        $response = $this->connector->send(new GetMeRequest);

        $data = $response->json('data');

        if (! is_array($data)) {
            throw new RuntimeException('Forge /me response did not include a `data` object.');
        }

        return User::from($data);
    }
}
