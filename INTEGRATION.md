# Issue #42 — Logs

## Spec endpoints found (docs/forge.openapi.json)
Logs are **raw-content fetches** (JSON:API GET + async DELETE). There is **no
list endpoint** — callers must already know the key/segment. Modeled on the
`DaemonResource::log(): string` raw-content pattern, not the SSH-key list pattern.

| Scope | Verb | Path | Response |
|-------|------|------|----------|
| Server | GET | `/orgs/{org}/servers/{server}/logs/{key}` | `ServerLogResource` — content at `data.attributes.content`; `meta.log` |
| Server | DELETE | same | 204 empty |
| Site | GET | `/orgs/{org}/servers/{server}/sites/{site}/logs/nginx-access` | `NginxAccessLogResource` — `data.attributes.content` |
| Site | GET | `.../sites/{site}/logs/nginx-error` | `NginxErrorLogResource` — `data.attributes.content` |
| Site | DELETE | both above | 202 empty |

All require scope `server:manage-logs`. Site log segments are **fixed** (only
`nginx-access` / `nginx-error`) → modeled as `Enums\SiteLog`. Server `{key}` is
a free-form string.

## Built (production)
- `Requests/Servers/GetServerLog`, `DeleteServerLog`, `GetSiteLog`, `DeleteSiteLog`
- `Enums/SiteLog` (`nginx-access` / `nginx-error`)
- `Resources/ServerLogsResource` — `get(string $key): string`, `delete(string $key): void`
- `Resources/SiteLogsResource` — `nginxAccess()/nginxError(): string`, `deleteNginxAccess()/deleteNginxError(): void`

## (1) Accessors to wire
On **`ServerResource`**:
```php
public function logs(): ServerLogsResource
{
    return new ServerLogsResource($this->connector, $this->organization, $this->id);
}
```
On **`SiteResource`**:
```php
public function logs(): SiteLogsResource
{
    return new SiteLogsResource($this->connector, $this->organization, $this->serverId, $this->siteId);
}
```
(Not done here — issue forbids editing `ServerResource`/`SiteResource`.)

## (2) Lifecycle tests + fixtures to record
Fixture-backed `MockClient` tests (mirror `DaemonResourceTest`): `logs()->get('nginx')`,
`logs()->nginxAccess()`, `nginxError()` return strings; delete methods send DELETE.
Fixtures to record (live): `logs/server-get`, `logs/site-nginx-access`,
`logs/site-nginx-error`. Reuse/extend a `LogFixture` subclass of `ForgeFixture`.

## (3) CRITICAL redaction plan
Log bodies (`data.attributes.content`) WILL leak secrets, IPs, paths, tokens,
env vars. A dedicated `LogFixture extends ForgeFixture` MUST redact:
- `content` → `REDACTED` (override `defineSensitiveJsonParameters()`, like
  `DaemonFixture`).
- Also scrub the raw fixture body via `defineSensitiveHeaders()` / a body
  callback for IPv4/IPv6, absolute paths, and bearer tokens, since content is a
  single opaque string (not nested JSON keys). Manually review every recorded
  fixture before commit.

## (4) Ambiguity
- No "list logs" endpoint exists; `{key}` is opaque (server side decides valid keys).
- `ServerLogResource.attributes` is not `required` in the spec → `get()` defends
  with `is_string(...) ? : ''` (same as Daemon `log()`).
- Server DELETE is documented 204, site DELETE 202 — both empty-body, returns `void`.
