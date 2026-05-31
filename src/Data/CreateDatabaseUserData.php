<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Data;

/**
 * Input payload for POST /orgs/{org}/servers/{server}/database/users.
 *
 * `name` and `password` are required. `readOnly` toggles read-only grants
 * on the assigned databases. `databaseIds` is a list of `DatabaseResource`
 * ids (integers, per the OpenAPI spec) the user should be granted access
 * to — omit to create the user with no grants.
 */
final readonly class CreateDatabaseUserData
{
    /**
     * @param  list<int>|null  $databaseIds
     */
    public function __construct(
        public string $name,
        public string $password,
        public ?bool $readOnly = null,
        public ?array $databaseIds = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $payload = [
            'name' => $this->name,
            'password' => $this->password,
        ];

        if ($this->readOnly !== null) {
            $payload['read_only'] = $this->readOnly;
        }

        if ($this->databaseIds !== null) {
            $payload['database_ids'] = $this->databaseIds;
        }

        return $payload;
    }
}
