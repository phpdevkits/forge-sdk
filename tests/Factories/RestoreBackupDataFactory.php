<?php

declare(strict_types=1);

namespace Tests\Factories;

use FBarrento\DataFactory\Factory;
use Override;
use PhpDevKits\ForgeSdk\Data\RestoreBackupData;

/**
 * @extends Factory<RestoreBackupData>
 */
final class RestoreBackupDataFactory extends Factory
{
    /** @var class-string<RestoreBackupData> */
    protected string $dataObject = RestoreBackupData::class;

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function definition(): array
    {
        return [
            'databaseId' => $this->fake->numberBetween(1, 99_999),
        ];
    }
}
