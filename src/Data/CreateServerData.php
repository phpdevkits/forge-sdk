<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Data;

use PhpDevKits\ForgeSdk\Enums\DatabaseType;
use PhpDevKits\ForgeSdk\Enums\PhpVersion;
use PhpDevKits\ForgeSdk\Enums\ServerType;
use PhpDevKits\ForgeSdk\Enums\UbuntuVersion;

/**
 * Input payload for POST /orgs/{org}/servers.
 *
 * Top-level required fields (name, provider, type, ubuntuVersion) plus the
 * matching provider sub-object — start with Hetzner; add AWS/Akamai/etc.
 * here as the SDK grows.
 */
final readonly class CreateServerData
{
    public function __construct(
        public string $name,
        public string $provider,
        public ServerType $type,
        public UbuntuVersion $ubuntuVersion,
        public ?int $credentialId = null,
        public ?PhpVersion $phpVersion = null,
        public ?DatabaseType $databaseType = null,
        public ?HetznerServerConfig $hetzner = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $payload = [
            'name' => $this->name,
            'provider' => $this->provider,
            'type' => $this->type->value,
            'ubuntu_version' => $this->ubuntuVersion->value,
        ];

        if ($this->credentialId !== null) {
            $payload['credential_id'] = $this->credentialId;
        }

        if ($this->phpVersion instanceof PhpVersion) {
            $payload['php_version'] = $this->phpVersion->value;
        }

        if ($this->databaseType instanceof DatabaseType) {
            $payload['database_type'] = $this->databaseType->value;
        }

        if ($this->hetzner instanceof HetznerServerConfig) {
            $payload['hetzner'] = $this->hetzner->toArray();
        }

        return $payload;
    }
}
