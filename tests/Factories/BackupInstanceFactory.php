<?php

declare(strict_types=1);

namespace Tests\Factories;

use DateTimeImmutable;
use FBarrento\DataFactory\Factory;
use Override;
use PhpDevKits\ForgeSdk\Data\BackupInstance;

/**
 * @extends Factory<BackupInstance>
 */
final class BackupInstanceFactory extends Factory
{
    /** @var class-string<BackupInstance> */
    protected string $dataObject = BackupInstance::class;

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function definition(): array
    {
        return [
            'id' => (string) $this->fake->numberBetween(1, 99_999),
            'status' => $this->fake->randomElement(['finished', 'pending', 'failed']),
            'isPartial' => false,
            'size' => $this->fake->numberBetween(1_024, 1_073_741_824),
            'finishedAt' => new DateTimeImmutable('-1 hour'),
        ];
    }
}
