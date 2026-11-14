# Monitors slice — integration notes (issue #39)

Server-scoped Monitors: `GET/POST /orgs/{org}/servers/{server}/monitors`, `GET/DELETE /monitors/{monitor}`.

## 1. Accessors to add to `ServerResource`
```php
public function monitors(): MonitorsResource
{
    return new MonitorsResource($this->connector, $this->organization, $this->serverId);
}

public function monitor(int|string $monitorId): MonitorResource
{
    return new MonitorResource($this->connector, $this->organization, $this->serverId, $monitorId);
}
```
Mirror the existing `sshKeys()` / `sshKey($id)` wiring. `MonitorsResource` exposes
`all()`, `iterate()`, `create()`; `MonitorResource` exposes `get()`, `delete()`.

## 2. Lifecycle tests + fixtures to record
Add `tests/Feature/Forge/` coverage (ForgeFixture, never ad-hoc mocks). Fixtures to record live:
- `monitors/index` — `all()` returns `Page<Monitor>` (paginated).
- `monitors/store` — `create()` (202 + `MonitorResource` data → hydrated `Monitor`).
- `monitors/show` — `get()` single monitor.
- `monitors/destroy` — `delete()` (async; assert no throw).
Plus an `iterate()` two-page walk reusing the index fixture cursor.

## 3. Redaction
`notify` is a real email address (PII) — redact to `redacted@example.com` in every
recorded fixture. Bearer token in request headers must be stripped as usual. IDs
and thresholds are non-sensitive.

## 4. Spec ambiguities / decisions
- `create` is `async` but the 202 body DOES include `MonitorResource` (unlike SSH
  keys' empty 202), so `create()` returns a hydrated `Monitor`. Confirm live.
- `attributes.status` uses the shared `ResourceState` enum; `state` uses
  `MonitorState`. Both kept as plain strings on the DTO (forward-compat), with
  convenience enums `MonitorState`, `MonitorMetricType`, `MonitorOperator`.
- `minutes` and `state_changed_at` are nullable per spec.
- `threshold` is a JSON number; DTO coerces int|float to `float`.

## Files
DTOs `Data/Monitor`, `Data/CreateMonitorData`, `Data/ListMonitorsOptions`; enums
`Enums/Monitor{MetricType,Operator,State}`; requests `Requests/Servers/{Get,Create,Delete}Monitor(s)`;
resources `Resources/Monitor(s)Resource`; factories `tests/Factories/{Monitor,CreateMonitorData}Factory`.
