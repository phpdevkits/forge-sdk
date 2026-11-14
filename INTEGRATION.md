# Integration notes — Issue #37 (Site Scheduled Jobs)

This worktree adds the site-scoped scheduled-jobs slice. The orchestrator owns wiring it into `SiteResource`, recording fixtures, and writing the lifecycle test. Everything below describes what still needs to happen outside this worktree.

## 1. Accessor methods to wire on `SiteResource`

Add to `src/Resources/SiteResource.php`:

```php
use PhpDevKits\ForgeSdk\Resources\SiteScheduledJobsResource;
use PhpDevKits\ForgeSdk\Resources\SiteScheduledJobResource;

public function scheduledJobs(): SiteScheduledJobsResource
{
    return new SiteScheduledJobsResource($this->connector, $this->organization, $this->serverId, $this->siteId);
}

public function scheduledJob(int|string $jobId): SiteScheduledJobResource
{
    return new SiteScheduledJobResource($this->connector, $this->organization, $this->serverId, $this->siteId, $jobId);
}
```

No changes needed in `src/Forge.php` or `composer.json`. No accessor on `ServerResource` (sites already chain through `site($id)`).

A small accessor unit test (mirroring `tests/Unit/Resources/SiteResourceTest.php` if present, or the `redirectRules()` accessor coverage) is appropriate:

- `tests/Unit/Resources/SiteResourceScheduledJobsAccessorsTest.php` — asserts `scheduledJobs()` returns a `SiteScheduledJobsResource` and `scheduledJob($id)` returns a `SiteScheduledJobResource`. Pure construction, no HTTP.

## 2. Resource lifecycle test + fixtures the orchestrator must record

Create `tests/Unit/Resources/SiteScheduledJobLifecycleTest.php` mirroring `tests/Unit/Resources/ScheduledJobLifecycleTest.php`, with these changes:

- `$siteId = ($_ENV['FORGE_TEST_SITE_ID'] ?? '') ?: '1';`
- Replace `Requests/Servers/{...}ScheduledJob*` imports with `Requests/Sites/{...}ScheduledJob*` (the site-scoped Requests created in this worktree).
- Replace `$forge->server($serverId)->scheduledJobs()` and `$forge->server($serverId)->scheduledJob($id)` with `$forge->server($serverId)->site($siteId)->scheduledJobs()` and `$forge->server($serverId)->site($siteId)->scheduledJob($id)`.
- Fixture name root: `site-scheduled-jobs/...` (keeps fixtures distinct from server-scoped):
  - `site-scheduled-jobs/create`
  - `site-scheduled-jobs/list`
  - `site-scheduled-jobs/get`
  - `site-scheduled-jobs/output`
  - `site-scheduled-jobs/delete`

A new fixture helper isn't required — reuse `Tests\Utils\ScheduledJobFixture` (its base directory just resolves the fixture filename).

Required env vars when recording: `FORGE_TEST_TOKEN`, `FORGE_TEST_ORG_SLUG`, `FORGE_TEST_SERVER_ID`, `FORGE_TEST_SITE_ID`.

Recommended recording payload: `command: 'echo sdk-test'`, `user: 'forge'`, `frequency: CronFrequency::Nightly`, `name: 'sdk-test-site-job'`. `try/finally` guarantees cleanup.

## 3. Spec ambiguities / decisions

- **Async create returning a body.** The OpenAPI spec marks `POST /orgs/{org}/servers/{server}/sites/{site}/scheduled-jobs` as `x-processingMode: async` but documents a `202` response carrying `{ data: JobResource }`. The merged server-scoped slice has the same ambiguity and chose to deserialize the body (its `ScheduledJobsResource::create()` returns `ScheduledJob`). The site slice mirrors that exactly — `SiteScheduledJobsResource::create()` returns `ScheduledJob` parsed from `data`. If the live API ever returns an empty 202 body, switch to the redirect-rules "create returns void, locate by attribute" pattern and update the lifecycle test accordingly.
- **Async delete.** `DELETE` is `x-processingMode: async` and returns `202` with no body. The mirrored `delete(): void` is correct; the lifecycle test should not assume immediate disappearance from a `list` if recording.
- **`filter[status]` values.** The spec doesn't enumerate valid statuses for `filter[status]` (mirrored from server-scoped `ListScheduledJobsOptions`). Caller-supplied strings pass through unchanged.

## Files added in this worktree

Requests (`src/Requests/Sites/`):
- `GetScheduledJobs.php`
- `GetScheduledJob.php`
- `CreateScheduledJob.php`
- `DeleteScheduledJob.php`
- `GetScheduledJobOutput.php`

Resources (`src/Resources/`):
- `SiteScheduledJobsResource.php`
- `SiteScheduledJobResource.php`

Tests (`tests/Unit/Requests/Sites/`):
- `GetScheduledJobsTest.php`
- `GetScheduledJobTest.php`
- `CreateScheduledJobTest.php`
- `DeleteScheduledJobTest.php`
- `GetScheduledJobOutputTest.php`

DTOs/enum reused unchanged from PR #50: `Data/ScheduledJob`, `Data/CreateScheduledJobData`, `Data/ListScheduledJobsOptions`, `Enums/CronFrequency`.

Quality gates: `vendor/bin/pint` passed, `vendor/bin/phpstan` 0 errors, `vendor/bin/pest` 13/13 passing on the new files (no-coverage).
