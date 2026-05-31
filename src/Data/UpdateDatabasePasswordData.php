<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Data;

/**
 * Input payload for PUT /orgs/{org}/servers/{server}/database/password.
 *
 * `password` is the new database password Forge should remember for the
 * server. Note: per the Forge docs, this only updates the password Forge
 * thinks is the password — it does NOT change the password on the database
 * server itself. Use this only after you have already rotated the password
 * out-of-band and want Forge's stored copy to catch up.
 */
final readonly class UpdateDatabasePasswordData
{
    public function __construct(
        public string $password,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return ['password' => $this->password];
    }
}
