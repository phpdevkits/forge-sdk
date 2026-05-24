<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Data;

/**
 * Input payload for PUT /orgs/{org}/servers/{server}/sites/{site}/environment.
 *
 * `content` is the raw `.env` file body; it is sent as the `environment`
 * field per the Forge API. The remaining fields are optional toggles.
 */
final readonly class UpdateEnvironmentData
{
    public function __construct(
        public string $content,
        public ?bool $cache = null,
        public ?bool $queues = null,
        public ?string $encryptionKey = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $payload = ['environment' => $this->content];

        if ($this->cache !== null) {
            $payload['cache'] = $this->cache;
        }

        if ($this->queues !== null) {
            $payload['queues'] = $this->queues;
        }

        if ($this->encryptionKey !== null) {
            $payload['encryption_key'] = $this->encryptionKey;
        }

        return $payload;
    }
}
