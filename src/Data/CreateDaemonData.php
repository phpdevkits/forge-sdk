<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Data;

use PhpDevKits\ForgeSdk\Enums\DaemonUser;

/**
 * Input payload for POST /orgs/{org}/servers/{server}/background-processes.
 */
final readonly class CreateDaemonData
{
    public function __construct(
        public string $name,
        public string $command,
        public DaemonUser $user,
        public int $processes = 1,
        public ?int $siteId = null,
        public ?string $directory = null,
        public ?int $startSecs = null,
        public ?int $stopWaitSecs = null,
        public ?string $stopSignal = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $payload = [
            'name' => $this->name,
            'command' => $this->command,
            'user' => $this->user->value,
            'processes' => $this->processes,
        ];

        if ($this->siteId !== null) {
            $payload['site_id'] = $this->siteId;
        }

        if ($this->directory !== null) {
            $payload['directory'] = $this->directory;
        }

        if ($this->startSecs !== null) {
            $payload['startsecs'] = $this->startSecs;
        }

        if ($this->stopWaitSecs !== null) {
            $payload['stopwaitsecs'] = $this->stopWaitSecs;
        }

        if ($this->stopSignal !== null) {
            $payload['stopsignal'] = $this->stopSignal;
        }

        return $payload;
    }
}
