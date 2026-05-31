<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Data;

/**
 * Input payload for PUT /orgs/{org}/servers/{server}/database/users/{databaseUser}.
 *
 * All fields are optional per the OpenAPI spec. `password` resets the
 * user's password (nullable in the spec — null leaves it untouched).
 * `databaseIds` replaces the user's database grants with the provided list
 * of `DatabaseResource` ids.
 */
final readonly class UpdateDatabaseUserData
{
    /**
     * @param  list<int>|null  $databaseIds
     */
    public function __construct(
        public ?string $password = null,
        public ?array $databaseIds = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $payload = [];

        if ($this->password !== null) {
            $payload['password'] = $this->password;
        }

        if ($this->databaseIds !== null) {
            $payload['database_ids'] = $this->databaseIds;
        }

        return $payload;
    }
}
