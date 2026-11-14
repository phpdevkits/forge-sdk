# Integration notes — Database schemas (issue #34)

Server-scoped resource covering `/orgs/{organization}/servers/{server}/database/schemas`.

## 1. Accessors to wire on `ServerResource`

`src/Resources/ServerResource.php` is intentionally NOT edited in this slice. Add (mirroring the
existing `sshKeys()` / `sshKey()` pair):

```php
public function databases(): DatabasesResource
{
    return new DatabasesResource($this->connector, $this->organization, $this->id);
}

public function database(int|string $databaseId): DatabaseResource
{
    return new DatabaseResource($this->connector, $this->organization, $this->id, $databaseId);
}
```

Imports: `PhpDevKits\ForgeSdk\Resources\DatabasesResource` and `...\DatabaseResource`.

Resource surface delivered:
- `DatabasesResource`: `all(?ListDatabasesOptions)`, `iterate(?ListDatabasesOptions)`,
  `create(CreateDatabaseData): Database`, `sync(): void`.
- `DatabaseResource`: `get(): Database`, `delete(): void`.

## 2. Lifecycle tests + fixtures to record (NOT done here — no credentials)

Use a database-type server. Server `1205620` (named "postgres") is suitable. Record fixtures via the
live workflow and add Feature lifecycle tests under `tests/Feature/Forge/`:

- `GetDatabases` — list, assert `Page<Database>` + cursor meta. Fixture: `databases/index`.
- `CreateDatabase` — POST `{name}` (and optionally `{name,user,password}`), assert 202 + returned
  `Database` with `status: installing`. Fixture: `databases/create`.
- `GetDatabase` — fetch the created id, assert hydration. Fixture: `databases/show`.
- `DeleteDatabase` — assert 202 Accepted (empty body). Fixture: `databases/delete`.
- `SyncDatabases` — POST synchronizations, assert 202 Accepted (empty body). Fixture:
  `databases/sync`.
- Pagination/iterate test once two real schemas exist (forge already ships one).

All HTTP-touching tests MUST go through a `ForgeFixture` subclass (no `MockResponse::make()`).

## 3. Redaction needed

`DatabaseResource` attributes (`id`, `name`, `status`, timestamps) carry no PII — minimal concern.
HOWEVER:
- `CreateDatabaseData` may carry `user` and especially `password`. The create REQUEST body fixture
  must redact `password` (and ideally `user`). Never commit a real password.
- The recorded response for `create` is the `DatabaseResource` (no password echoed back) — safe.
- Standard auth-header redaction (Bearer token) applies to every fixture as elsewhere.

## 4. Spec ambiguities

- POST `database/schemas` returns **202 with a `data: DatabaseResource` body** (per spec), unlike
  POST `ssh-keys` which returns 202 with an EMPTY body at runtime. I therefore made
  `DatabasesResource::create()` RETURN `Database` (hydrating `data`). VERIFY at record time: if Forge
  actually returns an empty 202 for schemas (as it does for ssh-keys), change `create()` to `void`
  and drop the `json('data')` hydration — see the SshKey precedent / comment.
- `synchronizations` POST takes no request body and returns 202 empty — modeled as a bodyless POST
  (mirrors `TriggerDeployment`). Confirm no payload is required.
- `DeleteDatabase` / `SyncDatabases` are async (202). Callers should poll `all()`/`get()` to observe
  `removing` -> gone / re-sync; documented in resource docblocks.
- List supports `sort` (`name`, `created_at`, `updated_at`, `-` prefix for desc) and
  `filter[name]` / `filter[status]`; `ListDatabasesOptions` exposes all of these plus page size/cursor.
- `Database` id is a string in JSON:API; path params accept `int|string` (consistent with the rest
  of the SDK).
