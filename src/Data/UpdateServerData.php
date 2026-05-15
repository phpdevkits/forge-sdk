<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Data;

/**
 * Input payload for PUT /orgs/{org}/servers/{id}. All fields optional —
 * `toArray()` strips nulls so partial updates only send what's set.
 */
final readonly class UpdateServerData
{
    /**
     * @param  ?list<string>  $tags
     */
    public function __construct(
        public ?string $name = null,
        public ?string $ipAddress = null,
        public ?string $privateIpAddress = null,
        public ?string $timezone = null,
        public ?array $tags = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $payload = [];

        if ($this->name !== null) {
            $payload['name'] = $this->name;
        }

        if ($this->ipAddress !== null) {
            $payload['ip_address'] = $this->ipAddress;
        }

        if ($this->privateIpAddress !== null) {
            $payload['private_ip_address'] = $this->privateIpAddress;
        }

        if ($this->timezone !== null) {
            $payload['timezone'] = $this->timezone;
        }

        if ($this->tags !== null) {
            $payload['tags'] = $this->tags;
        }

        return $payload;
    }
}
