<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Data;

use InvalidArgumentException;
use JsonSerializable;
use Override;

/**
 * The `maintenance_mode` sub-object on a {@see Site}.
 */
final readonly class SiteMaintenanceMode implements JsonSerializable
{
    public function __construct(
        public ?bool $enabled,
        public ?string $status,
    ) {}

    /**
     * @param  array<array-key, mixed>  $data
     */
    public static function from(array $data): self
    {
        // Freshly-created sites return `enabled: null`; treat it as nullable.
        $enabled = $data['enabled'] ?? null;
        if ($enabled !== null && ! is_bool($enabled)) {
            throw new InvalidArgumentException('SiteMaintenanceMode `enabled` must be a boolean or null.');
        }

        $status = $data['status'] ?? null;
        if ($status !== null && ! is_string($status)) {
            throw new InvalidArgumentException('SiteMaintenanceMode `status` must be a string or null.');
        }

        return new self(enabled: $enabled, status: $status);
    }

    /**
     * @return array{enabled: ?bool, status: ?string}
     */
    #[Override]
    public function jsonSerialize(): array
    {
        return [
            'enabled' => $this->enabled,
            'status' => $this->status,
        ];
    }
}
