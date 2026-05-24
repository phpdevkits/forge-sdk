<?php

declare(strict_types=1);

namespace Tests\Factories;

use DateTimeImmutable;
use FBarrento\DataFactory\Factory;
use Override;
use PhpDevKits\ForgeSdk\Data\FirewallRule;

/**
 * @extends Factory<FirewallRule>
 */
final class FirewallRuleFactory extends Factory
{
    /** @var class-string<FirewallRule> */
    protected string $dataObject = FirewallRule::class;

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function definition(): array
    {
        return [
            'id' => (string) $this->fake->numberBetween(1, 99_999),
            'name' => $this->fake->randomElement(['Allow HTTP', 'Allow HTTPS', 'Allow MySQL']),
            'port' => (string) $this->fake->randomElement(['80', '443', '3306']),
            'type' => $this->fake->randomElement(['allow', 'deny']),
            'ipAddress' => $this->fake->ipv4(),
            'status' => $this->fake->randomElement(['installed', 'installing']),
            'createdAt' => new DateTimeImmutable('-1 month'),
            'updatedAt' => new DateTimeImmutable('-1 day'),
        ];
    }
}
