<?php

declare(strict_types=1);

namespace Tests\Factories;

use DateTimeImmutable;
use FBarrento\DataFactory\Factory;
use Override;
use PhpDevKits\ForgeSdk\Data\Monitor;
use PhpDevKits\ForgeSdk\Enums\MonitorMetricType;
use PhpDevKits\ForgeSdk\Enums\MonitorOperator;
use PhpDevKits\ForgeSdk\Enums\MonitorState;

/**
 * @extends Factory<Monitor>
 */
final class MonitorFactory extends Factory
{
    /** @var class-string<Monitor> */
    protected string $dataObject = Monitor::class;

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function definition(): array
    {
        return [
            'id' => (string) $this->fake->numberBetween(1, 99_999),
            'type' => $this->fake->randomElement(MonitorMetricType::cases())->value,
            'operator' => $this->fake->randomElement(MonitorOperator::cases())->value,
            'threshold' => (float) $this->fake->numberBetween(1, 100),
            'minutes' => $this->fake->numberBetween(1, 60),
            'notify' => $this->fake->safeEmail(),
            'status' => 'installed',
            'state' => $this->fake->randomElement(MonitorState::cases())->value,
            'stateChangedAt' => new DateTimeImmutable('-1 day'),
            'createdAt' => new DateTimeImmutable('-1 month'),
            'updatedAt' => new DateTimeImmutable('-1 day'),
        ];
    }
}
