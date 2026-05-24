<?php

declare(strict_types=1);

namespace Tests\Factories;

use FBarrento\DataFactory\Factory;
use Override;
use PhpDevKits\ForgeSdk\Data\CreateMonitorData;
use PhpDevKits\ForgeSdk\Enums\MonitorMetricType;
use PhpDevKits\ForgeSdk\Enums\MonitorOperator;

/**
 * @extends Factory<CreateMonitorData>
 */
final class CreateMonitorDataFactory extends Factory
{
    /** @var class-string<CreateMonitorData> */
    protected string $dataObject = CreateMonitorData::class;

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function definition(): array
    {
        return [
            'type' => MonitorMetricType::CpuLoad,
            'operator' => MonitorOperator::GreaterThanOrEqual,
            'threshold' => 90.0,
            'notify' => $this->fake->safeEmail(),
            'minutes' => 1,
        ];
    }
}
