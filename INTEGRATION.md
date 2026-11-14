# Scheduled Jobs (server scope) — integration notes (#36)

## 1. Accessors to add to `ServerResource` (NOT edited here)
```php
public function scheduledJobs(): ScheduledJobsResource
{
    return new ScheduledJobsResource($this->connector, $this->organization, $this->id);
}

public function scheduledJob(int|string $jobId): ScheduledJobResource
{
    return new ScheduledJobResource($this->connector, $this->organization, $this->id, $jobId);
}
```
Mirror the existing `daemons()` / `daemon($id)` pair. `ScheduledJobsResource` exposes `all()/iterate()/create()`; `ScheduledJobResource` exposes `get()/delete()/output(): string`.

## 2. Lifecycle tests + fixtures to record (live, NOT done here)
- `tests/Feature/Forge/ScheduledJobsTest.php`: list (`all`/`iterate` pagination), `create` (echoes `JobResource`, 202 async), `get`, `output`, `delete`.
- Fixtures via `ForgeFixture` only — no `MockResponse::make()`. Record: `scheduled-jobs.index`, `.store`, `.show`, `.output`, `.destroy`.
- Note: create/delete are **async (202)**; `create()` returns the `JobResource` body, `delete()` returns void. Status starts `installing`.

## 3. Redaction
- `JobOutputResource.attributes.output` echoes raw command stdout/stderr — **redact** the `data.attributes.output` value in the `.output` fixture (replace with a placeholder). Also scrub server IPs/hostnames in `links.self` as elsewhere.

## 4. DTOs reusable by site scope (#37)
- `Data/ScheduledJob`, `Data/CreateScheduledJobData`, `Data/ListScheduledJobsOptions`, and `Enums/CronFrequency` are endpoint-agnostic (no server-specific fields) — reuse verbatim. #37 only adds site-scoped Requests under `Requests/Sites/` and `SiteScheduledJob(s)Resource`.

## 5. Spec ambiguities
- `JobResource.required` only lists `id/type/links`; `attributes` is technically optional. We treat `attributes` as required (mirrors `Daemon`).
- `attributes.user` on create is a free `string` in the spec (not the closed Root/Forge daemon enum) — modeled as `string`.
- `next_run_time`/`cron` are non-nullable `string` per spec but not date-typed; kept as raw strings. `name`, `created_at`, `updated_at` are nullable.
- `grace_period`/`frequency` use closed enums (`HeartbeatGracePeriod` 1/2/5/10/30/60; `CronFrequency`). `gracePeriod` modeled as plain `?int` (input only); `CronFrequency` enum added.
