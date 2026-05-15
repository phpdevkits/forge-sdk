<?php

declare(strict_types=1);

namespace Tests\Factories;

use DateTimeImmutable;
use FBarrento\DataFactory\Factory;
use Override;
use PhpDevKits\ForgeSdk\Data\Server;

/**
 * @extends Factory<Server>
 */
final class ServerFactory extends Factory
{
    /** @var class-string<Server> */
    protected string $dataObject = Server::class;

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function definition(): array
    {
        return [
            'id' => (string) $this->fake->numberBetween(1, 99_999),
            'credentialId' => $this->fake->numberBetween(1, 9_999),
            'name' => $this->fake->bothify('production-web-##'),
            'slug' => $this->fake->slug(2),
            'type' => $this->fake->randomElement(['app', 'web', 'worker', 'database', 'cache']),
            'ubuntuVersion' => $this->fake->randomElement(['22.04', '24.04']),
            'sshPort' => 22,
            'provider' => $this->fake->randomElement(['digitalocean', 'aws', 'hetzner', 'akamai']),
            'identifier' => (string) $this->fake->numberBetween(100_000, 999_999),
            'size' => $this->fake->bothify('s-?vcpu-#gb'),
            'region' => $this->fake->randomElement(['nyc1', 'fra1', 'sfo3', 'us-east-1']),
            'phpVersion' => $this->fake->randomElement(['php82', 'php83', 'php84']),
            'phpCliVersion' => $this->fake->randomElement(['php82', 'php83', 'php84']),
            'opcacheStatus' => $this->fake->randomElement(['enabled', 'disabled', null]),
            'databaseType' => $this->fake->randomElement(['mysql', 'postgres', null]),
            'dbStatus' => $this->fake->randomElement(['installed', 'installing', null]),
            'redisStatus' => $this->fake->randomElement(['installed', null]),
            'ipAddress' => $this->fake->ipv4(),
            'privateIpAddress' => $this->fake->ipv4(),
            'revoked' => false,
            'createdAt' => new DateTimeImmutable('-1 year'),
            'updatedAt' => new DateTimeImmutable('-1 day'),
            'connectionStatus' => 'connected',
            'timezone' => 'UTC',
            'localPublicKey' => null,
            'isReady' => true,
        ];
    }
}
