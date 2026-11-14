# Backup Instances + Restores — Integration Notes (issue #46)

## 1. Accessor chain to wire (on `BackupConfigurationResource`, from #45)

```php
public function instances(): BackupInstancesResource
{
    return new BackupInstancesResource($this->connector, $this->organization, $this->serverId, $this->backupConfigurationId);
}

public function instance(int|string $backupId): BackupInstanceResource
{
    return new BackupInstanceResource($this->connector, $this->organization, $this->serverId, $this->backupConfigurationId, $backupId);
}
```

No changes to `ServerResource` or `Forge.php` from this slice.

## 2. Lifecycle / fixture plan

- `instances()->all|iterate|trigger`, `instance($id)->get|delete`: safe to record;
  `trigger`/`delete` are async (202) — poll `get()` to settle.
- `instance($id)->restore()`: **DESTRUCTIVE — overwrites the target database.**
  Do NOT record without explicit user approval. Gate behind a
  `FORGE_TEST_ALLOW_RESTORE=1` env var and a throwaway database; otherwise
  hand-craft the fixture from a known-good 202.

## 3. Redaction (for the eventual `BackupFixture`)

- `attributes.size` → `0`; `attributes.finished_at` → fixed ISO date.
- `links.self` URLs → `https://forge.test/...` to drop org slug + ids.
- Inbound `RestoreBackup` body: redact `database_id` → `1`.

## 4. Spec ambiguities

- **Trigger response body**: spec says 202 with empty description; `trigger()`
  hydrates from `data` mirroring `DeploymentsResource::trigger()`. If the live
  API returns no body, switch to `void`.
- **`is_partial` type**: spec says `string`; modelled as `bool` (the `is_`
  prefix is decisive). Revisit if hydration throws.
- **Restore body**: spec only specifies required `database_id` (int) — confirm
  no hidden fields during first approved live restore.
