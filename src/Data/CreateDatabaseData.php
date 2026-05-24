<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Data;

/**
 * Input payload for POST /orgs/{org}/servers/{server}/database/schemas.
 *
 * `user` and `password` are only needed when a database user should be
 * created alongside the schema; `password` is only used when `user` is set.
 */
final readonly class CreateDatabaseData
{
    public function __construct(
        public string $name,
        public ?string $user = null,
        public ?string $password = null,
    ) {}

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        $payload = [
            'name' => $this->name,
        ];

        if ($this->user !== null) {
            $payload['user'] = $this->user;
        }

        if ($this->password !== null) {
            $payload['password'] = $this->password;
        }

        return $payload;
    }
}
