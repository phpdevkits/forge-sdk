<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Data;

/**
 * Input payload for PUT /orgs/{org}/servers/{server}/background-processes/{id}.
 *
 * Forge requires `name` on update; `config` (raw supervisor config) is
 * optional.
 */
final readonly class UpdateDaemonData
{
    public function __construct(
        public string $name,
        public ?string $config = null,
    ) {}

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        $payload = ['name' => $this->name];

        if ($this->config !== null) {
            $payload['config'] = $this->config;
        }

        return $payload;
    }
}
