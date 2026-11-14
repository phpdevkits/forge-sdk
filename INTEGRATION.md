# Backup Configurations — Integration handoff

## 1. Accessors to add on `ServerResource`

```php
public function backupConfigurations(): BackupConfigurationsResource
{
    return new BackupConfigurationsResource($this->connector, $this->organization, $this->serverId);
}

public function backupConfiguration(int|string $id): BackupConfigurationResource
{
    return new BackupConfigurationResource($this->connector, $this->organization, $this->serverId, $id);
}
```

`backupConfigurations()` exposes `all()` / `iterate()` / `create()`. `backupConfiguration($id)` exposes `get()` / `update()` / `delete()`.

## 2. Lifecycle test + fixtures the orchestrator must record

**Suggested file**: `tests/Unit/Resources/BackupConfigurationLifecycleTest.php`.

Suggested flow (mirrors `ScheduledJobLifecycleTest`): `list (empty) -> create -> list (one) -> get -> update -> get (updated) -> delete -> list (empty)`.

Fixtures to record (under `tests/Fixtures/Saloon/backup-configurations/`):
- `list-empty.json`, `create.json` (202), `list-one.json`, `get.json`, `update.json` (202), `get-updated.json`, `delete.json` (202), `list-empty-after.json`.

**Recording is gated** on the org having a `StorageProvider` already configured (S3/Spaces/Hetzner/OVH/Scaleway/custom) and at least one installed database schema on the target server. Live recording may be blocked if the test Forge org has no storage credentials wired up — the orchestrator should provision an `s3` storage provider out-of-band first, then pass its `storage_provider_id` (and at least one `database_id`) into the lifecycle test via env vars.

## 3. Redaction plan (CRITICAL — storage credentials)

Add a `BackupConfigurationFixture` (under `tests/Utils/`) that mirrors the existing fixture redactors and scrubs **every storage-credential surface** before any JSON is committed:

- `attributes.bucket` -> `"sdk-bucket-placeholder"` (bucket names leak account topology).
- `attributes.directory` -> `"forge-backups"` (may contain customer paths).
- `attributes.notify_email` -> `"ops@example.com"`.
- `attributes.storage_provider_id` -> stable synthetic int (e.g. `1`).
- `attributes.database_ids` -> stable `[1]` (recorded ids leak schema layout).
- `attributes.next_run_time` -> stable timestamp.
- Any `links.self` URL fragments -> rewrite host + ids.
- For the **input** payloads captured by Saloon (CreateBody / UpdateBody): same fields, plus `notification_email`.
- **Never** record any response containing access keys, secret keys, host, or username — if a future `StorageProvider` endpoint slice surfaces those, redact at source.

The recording wrapper should fail loudly (throw) if it encounters AWS-shaped access keys (`AKIA*`) or `secret_access_key`/`access_key_id` in any payload — defense-in-depth against a Forge response shape change leaking secrets.

## 4. Spec ambiguities for the orchestrator

1. **No inline credentials**: `CreateBackupConfigurationRequest` only references `storage_provider_id` (integer). The credential/region/bucket-policy details live on the separate `StorageProvider` resource (not in this slice). Implication: a lifecycle test cannot self-provision — it depends on a pre-existing storage provider.
2. **`bucket` overrideable per config**: spec lets `bucket` be passed in addition to `storage_provider_id`. Unclear whether this overrides the provider's default bucket or whether Forge rejects it for non-S3-like providers. Verify on recording.
3. **Async create/update, no response body**: POST and PUT both return 202 with empty body. Mirrored the SshKeys pattern — `create()` and `update()` return `void` and callers must re-`get()` to observe state. The spec lists `BackupConfigurationResource` as the schema but the runtime is empty.
4. **`day` / `time` / `cron` cross-field rules**: spec marks all three optional but they're implicitly mutually-coupled to `frequency` (weekly -> day+time; daily -> time; custom -> cron). DTO accepts any combination; Forge will 422 on bad combos.
5. **Output `provider` field is open string**: the response uses an unconstrained string for `provider`, even though `BackupProvider` is a defined enum. Kept the DTO field as `string` (deliberate, per skill rule).
6. **`day_of_week` integer vs `day` string-of-digit**: input `day` is `"0"`..`"6"` (string enum); output `day_of_week` is integer-or-null. Asymmetric on the wire; mirrored faithfully.

## Summary (<150 words)

Adds the server-scoped Backup Configurations slice: `BackupConfiguration` output DTO + `Create`/`Update` input DTOs + `ListBackupConfigurationsOptions` + `BackupFrequency` enum (hourly/daily/weekly/custom, spec-defined). Five requests (GET list, GET item, POST create, PUT update, DELETE) under `Requests/Servers/`. Two resources: `BackupConfigurationsResource` (all/iterate/create) and `BackupConfigurationResource` (get/update/delete). Per-DTO factories and 41 credential-free unit tests covering DTO hydration happy + each throw + jsonSerialize, options toQuery, create/update toArray, and request URL/method/body. POST and PUT are async (202, empty body) so `create()` and `update()` return void — mirrors the SshKeys precedent. `ServerResource` accessor wiring is NOT touched per the brief; orchestrator must add `backupConfigurations()` / `backupConfiguration($id)`. Lifecycle recording is gated on a pre-existing `StorageProvider` and database schema; redaction must scrub bucket/directory/notify_email/storage_provider_id/database_ids before any fixture is committed.
