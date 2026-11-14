# Integration notes — issue #40 (Site Nginx configuration)

Production classes + factories + credential-free unit tests only. The
orchestrator wires the accessor and records the lifecycle fixtures. No shared
files (`SiteResource`, `Forge`, `composer.json`) were edited.

## 1. Accessor to wire (on `src/Resources/SiteResource.php`)

Mirror `deploymentScript()`:

```php
public function nginxConfig(): NginxConfigResource
{
    return new NginxConfigResource($this->connector, $this->organization, $this->serverId, $this->siteId);
}
```

Usage: `$forge->server($id)->site($id)->nginxConfig()->get()` /`->update($data)`.

## 2. Lifecycle tests + fixtures to record (site 3210369)

| Request | Endpoint | Fixture |
|---|---|---|
| `GetNginxConfig` | `GET /orgs/{org}/servers/{server}/sites/{site}/nginx` | `nginx/config` |
| `UpdateNginxConfig` | `PUT .../sites/{site}/nginx` | `nginx/config-update` |

- `NginxConfigResource::get()` hydrates a `NginxConfig` (id + content).
- `NginxConfigResource::update()` returns `void` (PUT is async, 202 empty body);
  assert by sending then re-`get()`.

## 3. Fixture redaction

`get()` returns the full rendered vhost — high PII. Add an `NginxFixture`
extending `ForgeFixture` redacting `attributes.content` to a stub vhost, since
it embeds `server_name` (real domains), on-forge hostnames, and
`/home/forge/<site>` paths. Also redact any `links.self.href` carrying the org
slug / real ids.

## 4. Spec ambiguities

- **Issue framed `get(): string`, but the spec returns a structured JSON:API
  `NginxConfigResource`** (`id`, `type`, nullable `attributes.content`). To match
  the spec and the `DeploymentScript` slice, `get()` returns a `NginxConfig` DTO,
  not a raw string. Read the content via `->get()->content`.
- **PUT body key is `config`, not `content`** (`UpdateNginxConfigurationRequest`);
  `UpdateNginxConfigData` takes `content` but `toArray()` emits `['config' => ...]`.
- **PUT is async / 202 empty** so `update()` is `void`; `get()` is sync/200.
- Requests are namespaced `Requests\Servers` (existing convention) despite the
  `Sites` tag; mirrors `GetDeploymentScript`/`UpdateDeploymentScript`.
- Path `server`/`site` are `integer` in the spec but ids arrive as strings; both
  request constructors accept `int|string`.

## Files added

DTOs: `src/Data/{NginxConfig,UpdateNginxConfigData}.php`
Requests: `src/Requests/Servers/{GetNginxConfig,UpdateNginxConfig}.php`
Resource: `src/Resources/NginxConfigResource.php`
Factories: `tests/Factories/{NginxConfigFactory,UpdateNginxConfigDataFactory}.php`
Tests: `tests/Unit/Data/{NginxConfigTest,UpdateNginxConfigDataTest}.php`,
`tests/Unit/Requests/Servers/{GetNginxConfig,UpdateNginxConfig}Test.php`
