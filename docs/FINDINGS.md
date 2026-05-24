# Forge API findings

Running notes on places where Forge's runtime behavior diverges from
`docs/forge.openapi.json`. Each entry should record what the spec claims,
what the API actually does, and which SDK code accommodates the mismatch
— so the next person debugging a 422 (or a hydration crash) can find the
answer here instead of via another live-fire investigation.

When you discover one, append a new section with the date. Don't rewrite
history — old entries document the shape of the API at the time they
were recorded, even if Forge later corrects the spec.

---

## 2026-05-24 — Recorded fixtures leak resource ids / hostnames through URL paths

**Not a spec issue — a test-hygiene one.** The `ForgeFixture` leaf-key
redaction scrubbed `id` / `name` / `slug`, but real numeric resource ids
and on-forge hostnames survived inside `links.self.href`, `meta.path`,
and `web_directory` because the regex pass only covered `/orgs/<slug>`
(plus `/servers/<id>` in `ServerFixture`).

**Fix:** `ForgeFixture::defineSensitiveRegexPatterns()` now collapses every
nested resource-id segment (`servers|sites|background-processes|
deployments|ssh-keys|keys|providers|regions|sizes|credentials`) to `/1`
and rewrites `*.on-forge.com` → `example-site.on-forge.com`. No
credentials ever leaked (tokens/keys/passwords/cookies were always clean).
When adding a new nested resource, confirm its URL segment is in that
alternation.

---

## 2026-05-24 — Daemon update 500s unless `config` is sent

**Spec:** `UpdateBackgroundProcessRequest` requires `name`; `config` (raw
supervisor config) is optional.

**Runtime:** `PUT .../background-processes/{id}` with name-only returns
`500 Server Error`. With `name` + `config` → `202`. `config` is
effectively required.

**SDK impact:** `Data\UpdateDaemonData` keeps `config` nullable (faithful to
spec) but `DaemonResource::update()` documents that omitting it 500s.
Always set it.

---

## 2026-05-24 — Fresh daemons sit in `installing` → `installed` (never `running`); restart 500s while installing

**Runtime:** A just-created daemon reports `status: installing`, then
settles to `installed` (not `running`). `POST .../actions` (restart/etc.)
returns 500 until the daemon is `installed`.

**SDK impact:** the daemon lifecycle test waits on a live un-mocked
connector until `status !== 'installing'` before acting; skipped on
replay. `BackgroundProcessResource` attributes are also sparse —
command/user/directory/processes/status/created_at only (no `name` or
`updated_at`).

---

## 2026-05-24 — Deployment trigger / script live behind a real repo

**Runtime:** `POST .../deployments` (trigger) returns `202` with the new
deployment in the body. Deploying a `php`-type site whose repo is actually
Laravel fails (`vendor/autoload.php` missing) because the default deploy
script runs `artisan` before `composer install` — fixable via
`deploymentScript()->update()` (composer install first), which the
Deployments slice dogfooded. The `.on-forge.com` edge then 525s until an
SSL cert is issued (see issue #25).

**SDK impact:** `Data\Deployment.started_at` / `ended_at` are nullable
(queued deployments have neither, despite the spec marking them required).

---

## 2026-05-20 — Sites: four spec-vs-runtime divergences

1. **Individual-site GET is server-less.** `GET /orgs/{org}/sites/{site}`
   — the `/servers/{server}/sites/{site}` path only supports PUT/DELETE
   (GET there → 405). `GetSite` uses the server-less path; update/delete
   keep the server segment.
2. **`domain_mode` is runtime-required** on create (spec marks it
   optional), and on-forge sites need a single dotless subdomain `name`.
3. **Fresh sites return `aliases`, `deployment_status`, and
   `maintenance_mode.enabled` as null** despite required-non-null in spec
   — `Data\Site` / `Data\SiteMaintenanceMode` loosened accordingly.
4. **PUT update returns 202 with an empty body** → `SiteResource::update()`
   is `void`. A freshly-created site also briefly 405s on its
   individual routes while provisioning.

---

## 2026-05-19 — `POST .../ssh-keys` returns 202 with an empty body

**Spec:** documented as returning a `KeyResource`.

**Runtime:** `202 Accepted`, empty body, `text/html` content type — the
key installs asynchronously and no resource comes back.

**SDK impact:** `SshKeysResource::create()` is typed `void`; callers list
afterwards to recover the new key.

---

## 2026-05-15 — `hetzner.size_id` only accepts the size code, not the Forge id

**Spec:** `CreateServerRequest.hetzner.size_id` is `"type": "string"` with
no further constraint. Spec for the catalog also returns sizes with both
`id` (numeric Forge id, e.g. `3701`) and `code` (e.g. `cax11`).

**Runtime:** The create endpoint rejects `size_id: "3701"` with
`{"hetzner.size_id": ["The selected size is invalid."]}`. It accepts
`size_id: "cax11"` (the `code`).

**Asymmetry with `region_id`:** Forge accepts **any** of the three forms
for region — the Forge `id` (`102`), the `code` (`2`), or the `alt`
(`nbg1`). Size is strict to `code` only.

**SDK impact:** `Data\HetznerServerConfig.sizeId` is a plain `string`; we
don't normalize. Callers must pass the code form. Recording env var
`FORGE_TEST_HETZNER_SIZE` should be set to e.g. `cax11`.

---

## 2026-05-15 — `hetzner.network_id` is runtime-required despite optional in spec

**Spec:** `CreateServerRequest.hetzner.network_id` is `"type": "integer"`
and is *not* in the `required` list.

**Runtime:** The create endpoint rejects payloads without it:
`{"hetzner.network_id": ["The private network field is required."]}`.

**No `list networks` endpoint exists on the server side** — `GET
/orgs/{org}/servers/{server}/network` returns `{"data": []}` for our
test server, and `ServerResource.attributes` has no `network_id`. The
actual list of usable VPCs lives at:

```
GET /orgs/{org}/server-credentials/{credential}/regions/{region}/vpcs
```

That endpoint isn't wrapped by the SDK yet — if you need it, surface a
`ServerCredentialsResource` on `Forge` before relying on this finding in
a test.

**SDK impact:** `HetznerServerConfig.networkId` is typed `?int` so the
DTO compiles without it, but `CreateServer` will 422 if you actually
omit it. Recording requires `FORGE_TEST_HETZNER_NETWORK_ID` in `.env`.

---

## 2026-05-15 — Freshly-created (202 async) servers return `revoked: null` and `connection_status: null`

**Spec:** `ServerResource.attributes.revoked` is `"type": "boolean"` and
`connection_status` is `"type": "string"`; both are in the `required`
list.

**Runtime:** POST `/orgs/{org}/servers` returns 202 with the new server
in the body. On a fresh server those two fields come back as `null`
(other "not yet provisioned" fields like `ip_address`, `db_status`, etc.
are already nullable per spec — these two are the spec-but-not-runtime
mismatches).

**SDK impact:** `Data\Server.revoked` is `?bool` and `connection_status`
is `?string`. `optionalBool()` helper covers the new branch.

---

## 2026-05-15 — `meta.sudo_password` leaks on create response

**Not in the spec at all.** POST `/orgs/{org}/servers` returns the
generated SSH `sudo` password in `data.data.meta.sudo_password` — Forge
expects the caller to capture it on first create (you can't fetch it
again later via the GET endpoint).

**SDK impact:** Not yet surfaced on `Data\Server` — captured at the DTO
boundary it'd be lost. If client code needs it, expose it as a
ServerResource return type (a `CreatedServer` wrapping `Server` +
sudoPassword), or read it off the raw response before hydration.

**Fixture redaction:** `Tests\Utils\ServerFixture` replaces
`sudo_password` with the literal `REDACTED` at record time so it doesn't
land in committed fixtures.

---

## 2026-05-14 — Compute provider slugs / sizes / architectures are open strings, not enums

**Spec:** `ProviderResource.attributes.slug`, `ProviderSizeResource.attributes.disk_type`,
and `ProviderSizeResource.attributes.architecture` are all plain
`"type": "string"` with no `enum:` constraint.

**Runtime:** `disk_type` returns display labels with spaces (`"SSD"`,
`"NVMe SSD"`). `architecture` returns mixed casing (`"x86_64"`,
`"AMD64"`).

**SDK impact:** DTO fields stay `string`. `Enums\Provider` exists as a
*constant table* for ergonomic client code (e.g.
`$provider->slug === Provider::DigitalOcean->value`) — not as the DTO
type. Hydration stays lenient so a new provider Forge launches doesn't
break existing SDK installs. `DiskType` and `Architecture` enums are
intentionally not provided.
