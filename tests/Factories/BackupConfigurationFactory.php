<?php

declare(strict_types=1);

namespace Tests\Factories;

use FBarrento\DataFactory\Factory;
use Override;
use PhpDevKits\ForgeSdk\Data\BackupConfiguration;

/**
 * @extends Factory<BackupConfiguration>
 */
final class BackupConfigurationFactory extends Factory
{
    /** @var class-string<BackupConfiguration> */
    protected string $dataObject = BackupConfiguration::class;

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function definition(): array
    {
        return [
            'id' => (string) $this->fake->numberBetween(1, 99_999),
            'name' => $this->fake->bothify('backup-?#'),
            'storageProviderId' => $this->fake->numberBetween(1, 9_999),
            'provider' => $this->fake->randomElement(['s3', 'spaces', 'hetzner', 'ovh', 'scaleway', 'custom']),
            'bucket' => 'sdk-bucket-placeholder',
            'directory' => 'forge-backups',
            'schedule' => 'daily',
            'displayableSchedule' => 'Every day at 02:00',
            'nextRunTime' => '2026-06-01 02:00:00',
            'status' => $this->fake->randomElement(['installed', 'installing', 'removing']),
            'dayOfWeek' => null,
            'time' => '02:00',
            'cronSchedule' => null,
            'databaseIds' => [1, 2, 3],
            'retention' => 7,
            'notifyEmail' => null,
        ];
    }
}
