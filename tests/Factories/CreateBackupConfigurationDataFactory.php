<?php

declare(strict_types=1);

namespace Tests\Factories;

use FBarrento\DataFactory\Factory;
use Override;
use PhpDevKits\ForgeSdk\Data\CreateBackupConfigurationData;
use PhpDevKits\ForgeSdk\Enums\BackupFrequency;

/**
 * @extends Factory<CreateBackupConfigurationData>
 */
final class CreateBackupConfigurationDataFactory extends Factory
{
    /** @var class-string<CreateBackupConfigurationData> */
    protected string $dataObject = CreateBackupConfigurationData::class;

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function definition(): array
    {
        return [
            'storageProviderId' => $this->fake->numberBetween(1, 9_999),
            'frequency' => BackupFrequency::Daily,
            'retention' => 7,
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
