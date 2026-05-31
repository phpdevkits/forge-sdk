<?php

declare(strict_types=1);

namespace Tests\Factories;

use FBarrento\DataFactory\Factory;
use Override;
use PhpDevKits\ForgeSdk\Data\UpdateBackupConfigurationData;
use PhpDevKits\ForgeSdk\Enums\BackupFrequency;

/**
 * @extends Factory<UpdateBackupConfigurationData>
 */
final class UpdateBackupConfigurationDataFactory extends Factory
{
    /** @var class-string<UpdateBackupConfigurationData> */
    protected string $dataObject = UpdateBackupConfigurationData::class;

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function definition(): array
    {
        return [
            'storageProviderId' => $this->fake->numberBetween(1, 9_999),
            'frequency' => BackupFrequency::Daily,
            'retention' => 14,
            'databaseIds' => [1],
            'name' => null,
            'bucket' => null,
            'directory' => null,
            'day' => null,
            'time' => null,
            'cron' => null,
            'notificationEmail' => null,
        ];
    }
}
