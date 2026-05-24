<?php

declare(strict_types=1);

namespace Tests\Factories;

use DateTimeImmutable;
use FBarrento\DataFactory\Factory;
use Override;
use PhpDevKits\ForgeSdk\Data\Daemon;

/**
 * @extends Factory<Daemon>
 */
final class DaemonFactory extends Factory
{
    /** @var class-string<Daemon> */
    protected string $dataObject = Daemon::class;

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function definition(): array
    {
        return [
            'id' => (string) $this->fake->numberBetween(1, 99_999),
            'command' => 'php artisan queue:work',
            'user' => $this->fake->randomElement(['forge', 'root']),
            'directory' => '/home/forge/example.com',
            'processes' => $this->fake->numberBetween(1, 4),
            'status' => $this->fake->randomElement(['running', 'stopped']),
            'createdAt' => new DateTimeImmutable('-1 week'),
        ];
    }
}
