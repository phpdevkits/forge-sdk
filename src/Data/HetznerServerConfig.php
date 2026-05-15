<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Data;

/**
 * Hetzner-specific create-server config, nested under `hetzner` in the request body.
 *
 * Forge's create-server endpoint is provider-polymorphic — each cloud has its
 * own sub-object alongside the shared top-level fields. Each provider gets
 * its own typed config DTO; new providers land when the SDK needs them.
 */
final readonly class HetznerServerConfig
{
    public function __construct(
        public string $regionId,
        public string $sizeId,
        public ?int $networkId = null,
        public ?bool $enableDailyBackups = null,
    ) {}

    /**
     * @return array<string, int|string|bool>
     */
    public function toArray(): array
    {
        $payload = [
            'region_id' => $this->regionId,
            'size_id' => $this->sizeId,
        ];

        if ($this->networkId !== null) {
            $payload['network_id'] = $this->networkId;
        }

        if ($this->enableDailyBackups !== null) {
            $payload['enable_daily_backups'] = $this->enableDailyBackups;
        }

        return $payload;
    }
}
