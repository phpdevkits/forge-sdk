<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Data;

/**
 * Input payload for POST /orgs/{org}/servers/{server}/ssh-keys.
 *
 * `user` defaults to `forge` on the API side when omitted.
 */
final readonly class CreateSshKeyData
{
    public function __construct(
        public string $name,
        public string $key,
        public ?string $user = null,
    ) {}

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        $payload = [
            'name' => $this->name,
            'key' => $this->key,
        ];

        if ($this->user !== null) {
            $payload['user'] = $this->user;
        }

        return $payload;
    }
}
