<?php

declare(strict_types=1);

namespace Tests\Factories;

use DateTimeImmutable;
use FBarrento\DataFactory\Factory;
use Override;
use PhpDevKits\ForgeSdk\Data\ScheduledJob;

/**
 * @extends Factory<ScheduledJob>
 */
final class ScheduledJobFactory extends Factory
{
    /** @var class-string<ScheduledJob> */
    protected string $dataObject = ScheduledJob::class;

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function definition(): array
    {
        return [
            'id' => (string) $this->fake->numberBetween(1, 99_999),
            'name' => 'My scheduled job',
            'command' => 'php artisan schedule:run',
            'status' => $this->fake->randomElement(['installed', 'installing']),
            'user' => $this->fake->randomElement(['forge', 'root']),
            'frequency' => $this->fake->randomElement(['hourly', 'nightly', 'custom']),
            'cron' => '0 * * * *',
            'nextRunTime' => '2026-01-01 01:00:00',
            'createdAt' => new DateTimeImmutable('-1 week'),
            'updatedAt' => new DateTimeImmutable('-1 day'),
        ];
    }
}
