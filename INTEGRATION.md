# Integration: Site Environment File (issue #38)

This slice adds the site `.env` get/update endpoints. It mirrors the
deployment-script slice exactly. New production classes:

- `src/Data/UpdateEnvironmentData.php`
- `src/Requests/Servers/GetEnvironment.php` (`GET .../environment`)
- `src/Requests/Servers/UpdateEnvironment.php` (`PUT .../environment`)
- `src/Resources/EnvironmentResource.php`

Tests (credential-free, no fixtures/network):

- `tests/Unit/Data/UpdateEnvironmentDataTest.php`
- `tests/Unit/Requests/Servers/GetEnvironmentTest.php`
- `tests/Unit/Requests/Servers/UpdateEnvironmentTest.php`
- `tests/Factories/UpdateEnvironmentDataFactory.php`

## 1. Accessor to wire (NOT done here — I cannot edit `SiteResource`)

Add to `src/Resources/SiteResource.php`, next to `deploymentScript()`:

```php
public function environment(): EnvironmentResource
{
    return new EnvironmentResource($this->connector, $this->organization, $this->serverId, $this->siteId);
}
```

Import `use PhpDevKits\ForgeSdk\Resources\EnvironmentResource;`. Usage then
becomes `$forge->server($id)->site($id)->environment()->get()` /
`->update(new UpdateEnvironmentData(...))`.

## 2. Lifecycle tests + fixtures to record (site 3210369)

Mirror `tests/Unit/Resources/DeploymentScriptResourceTest.php` in a new
`tests/Unit/Resources/EnvironmentResourceTest.php`. Two fixtures to record
against a real site (recordable on site `3210369`):

- `environment` — `GetEnvironment` 200 body. Assert `get()` returns a
  non-empty string equal to the (redacted) recorded content.
- `environment-update` — `UpdateEnvironment` 202 with EMPTY body. `update()`
  returns void, so assert only that no exception is thrown (and optionally
  re-`get()`). Do NOT assert on a parsed body; there is none.

Use a new `EnvironmentFixture` (see §3) for both, e.g.
`new MockClient([GetEnvironment::class => new EnvironmentFixture('environment')])`.

## 3. CRITICAL redaction — the body IS all secrets

The GET response `data.attributes.content` is the entire raw `.env` file:
DB passwords, `APP_KEY`, API tokens, mail creds — every line is sensitive.
The default `ForgeFixture` leaf-key pass only scrubs known keys (`id`,
`email`, `slug`, ...); it will NOT touch `content`, so a naive recording
writes the live secrets straight to disk.

Add `tests/Utils/EnvironmentFixture.php` extending `ForgeFixture` and
OVERWRITE the whole `content` value with a static, fabricated `.env`. Because
the value is one opaque blob (not parseable keys), redact by total
replacement, not field-by-field:

```php
final class EnvironmentFixture extends ForgeFixture
{
    protected function defineSensitiveJsonParameters(): array
    {
        return [
            ...parent::defineSensitiveJsonParameters(),
            // Replace the ENTIRE .env blob — never try to scrub line-by-line.
            'content' => "APP_ENV=production\nAPP_KEY=base64:REDACTED\nDB_PASSWORD=REDACTED",
        ];
    }
}
```

Manual review checklist before committing the fixture JSON:
- `content` is the fabricated blob, not the recorded one.
- No real hostnames survive (the `*.on-forge.com` regex in `ForgeFixture`
  handles URL/path occurrences, but eyeball the blob too).
- No bearer token in the request `Authorization` header (ForgeFixture strips
  headers, but confirm).
- The PUT recording's request body `environment` field is ALSO the raw blob —
  if recording the update, scrub it the same way (it round-trips your secrets).

## 4. Spec ambiguities / decisions

- **Field name mismatch:** GET returns the content under
  `data.attributes.content`, but PUT expects it under `environment` (per
  `UpdateEnvironmentRequest`), NOT `content`. `UpdateEnvironmentData::content`
  serializes to the `environment` key — verified by unit test.
- **`get()` returns `string`, not a DTO:** per the issue brief
  (`get(): string`). The spec wraps it in a JSON:API `EnvironmentResource`
  object; `get()` unwraps `data.attributes.content`. No hydrated DTO / factory
  for the response is needed. If a future caller wants `id`/`links`, promote to
  an `Environment` DTO with a factory.
- **PUT is async, 202, empty body:** `update()` returns void (like
  `SiteResource::update()`); re-`get()` to read the new state.
- **Optional PUT fields:** `cache`, `queues`, `encryption_key` (nullable) are
  modeled as optional ctor args and only emitted when non-null. Their exact
  Forge semantics (cache config / restart queues) are not documented in the
  spec; only `environment` is required.

---

### Summary

Added the site environment-file slice mirroring deployment-script:
`UpdateEnvironmentData`, `GetEnvironment`/`UpdateEnvironment` requests, and
`EnvironmentResource` (`get(): string` unwrapping `data.attributes.content`;
`update()` returning void on the async 202). The PUT payload's content field is
`environment`, not `content` — a real spec gotcha covered by tests. Credential-
free unit tests cover `toArray()` serialization and request
endpoint/method/body; pint, phpstan, and the 11 unit tests are green. No
fixtures or network calls were made. `SiteResource::environment()` still needs
wiring (I cannot edit that file). The most important follow-up is redaction:
the `.env` body is entirely secret, so the recorder must overwrite the whole
`content` blob with a fabricated value via an `EnvironmentFixture` — never
scrub it line-by-line.
