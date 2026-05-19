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
