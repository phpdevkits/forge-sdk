<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk;

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
