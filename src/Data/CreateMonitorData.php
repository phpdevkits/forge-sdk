<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Data;

use PhpDevKits\ForgeSdk\Enums\MonitorMetricType;
use PhpDevKits\ForgeSdk\Enums\MonitorOperator;

/**
 * Input payload for POST /orgs/{org}/servers/{server}/monitors.
 */
final readonly class CreateMonitorData
{
    public function __construct(
        public MonitorMetricType $type,
        public MonitorOperator $operator,
        public float $threshold,
        public string $notify,
        public ?int $minutes = null,
    ) {}

    /**
     * @return array<string, float|int|string>
     */
    public function toArray(): array
    {
        $payload = [
            'type' => $this->type->value,
            'operator' => $this->operator->value,
            'threshold' => $this->threshold,
            'notify' => $this->notify,
        ];

        if ($this->minutes !== null) {
            $payload['minutes'] = $this->minutes;
        }

        return $payload;
    }
}
