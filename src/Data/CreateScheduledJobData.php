<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Data;

use PhpDevKits\ForgeSdk\Enums\CronFrequency;

/**
 * Input payload for POST .../scheduled-jobs (server- or site-scoped).
 */
final readonly class CreateScheduledJobData
{
    public function __construct(
        public string $command,
        public string $user,
        public CronFrequency $frequency,
        public ?string $name = null,
        public ?string $cron = null,
        public ?bool $heartbeat = null,
        public ?int $gracePeriod = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $payload = [
            'command' => $this->command,
            'user' => $this->user,
            'frequency' => $this->frequency->value,
        ];

        if ($this->name !== null) {
            $payload['name'] = $this->name;
        }

        if ($this->cron !== null) {
            $payload['cron'] = $this->cron;
        }

        if ($this->heartbeat !== null) {
            $payload['heartbeat'] = $this->heartbeat;
        }

        if ($this->gracePeriod !== null) {
            $payload['grace_period'] = $this->gracePeriod;
        }

        return $payload;
    }
}
