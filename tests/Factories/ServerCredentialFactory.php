<?php

declare(strict_types=1);

namespace Tests\Factories;

use DateTimeImmutable;
use FBarrento\DataFactory\Factory;
use Override;
use PhpDevKits\ForgeSdk\Data\ServerCredential;

/**
 * @extends Factory<ServerCredential>
 */
final class ServerCredentialFactory extends Factory
{
    /** @var class-string<ServerCredential> */
    protected string $dataObject = ServerCredential::class;

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function definition(): array
    {
        return [
            'id' => (string) $this->fake->numberBetween(1, 99_999),
            'name' => $this->fake->bothify('credential-?#'),
            'provider' => $this->fake->randomElement(['aws', 'hetzner', 'digitalocean', 'linode']),
            'inUse' => $this->fake->boolean(),
            'createdAt' => new DateTimeImmutable('-1 month'),
            'updatedAt' => new DateTimeImmutable('-1 day'),
        ];
    }
}
