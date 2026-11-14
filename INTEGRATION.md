# Database Users — integration handoff (issue #43)

## 1. Accessors to wire on `ServerResource`

```php
public function databaseUsers(): DatabaseUsersResource
{
    return new DatabaseUsersResource($this->connector, $this->organization, $this->id);
}

public function databaseUser(int|string $databaseUserId): DatabaseUserResource
{
    return new DatabaseUserResource($this->connector, $this->organization, $this->id, $databaseUserId);
}
```

Place them alongside the existing `databases()` / `database()` pair in `src/Resources/ServerResource.php`.

## 2. Lifecycle tests + fixtures to record

Use `FORGE_TEST_DATABASE_SERVER_ID=1205620`. Suggested lifecycle file `tests/Feature/Forge/DatabaseUsersLifecycleTest.php`, wrapped in try/finally so the user always gets deleted:

1. `create` (POST `/database/users`) → captures id, status `installing`. Fixture: `database-users/create.json`.
2. `all` / `iterate` (GET `/database/users`) → contains the new user. Fixture: `database-users/list.json`.
3. `get` (GET `/database/users/{id}`) once status reaches `installed`. Fixture: `database-users/get.json`.
4. `update` (PUT `/database/users/{id}`) rotating password and replacing `database_ids`. Fixture: `database-users/update.json`.
5. `delete` (DELETE `/database/users/{id}`) in `finally`. Fixture: `database-users/delete.json`.

A `tests/Utils/DatabaseUserFixture.php` (extending `ForgeFixture`) is needed — see redaction below.

## 3. CRITICAL redaction (passwords!)

Override `defineSensitiveJsonParameters()` to scrub:

- `password` → `'REDACTED'` (request body for create + update).
- `database_ids` → stable `[1, 2]` (real ids reveal account topology).
- `next_cursor` / `prev_cursor` → stable `'CURSOR-A'` (mirror `DatabaseFixture`).

The shared regex pass in `ForgeFixture` already collapses `/users/<n>` → `/users/1`, but verify it does because the path segment is `users` (already in the allow-list).

## 4. Spec ambiguities to confirm on first recording

- **Async create body**: spec says 202 returns the full `DatabaseUserResource`, mirroring databases. Verify Forge actually returns the body (SshKey breaks this contract — see `SshKeysResource` note). If empty, drop the return type and follow the SshKey pattern.
- **Grants type**: spec says `database_ids: integer[]` (not names, not schema strings). DTO is typed `list<int>`. Confirm Forge accepts integers, not stringified ids.
- **PUT update**: spec exposes it (returns 202 empty body — modelled as `void`). Confirm omitting `database_ids` leaves grants intact vs. wipes them.
- **`read_only` on update**: spec's `UpdateDatabaseUserRequest` lacks `read_only` (only `CreateDatabaseUserRequest` has it). DTO follows the spec, but worth probing whether Forge silently accepts it on PUT.
