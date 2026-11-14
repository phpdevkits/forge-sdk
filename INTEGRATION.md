# Issue #44 — Database Password (integration handoff)

## 1. Accessor the orchestrator must add
On `src/Resources/ServerResource.php`, alongside the existing nested
accessors (`databases()`, `database(...)`, etc.):

```php
public function databasePassword(): DatabasePasswordResource
{
    return new DatabasePasswordResource($this->connector, $this->organization, $this->id);
}
```

No plural variant — the endpoint is a singleton server sub-resource.

## 2. Lifecycle test + fixture (RECORD ONLY WITH EXPLICIT USER APPROVAL)
The slice ships unit tests only. A `tests/Feature/.../UpdateDatabasePasswordTest.php`
+ recorded `update.json` fixture would round-trip
`PUT /orgs/{org}/servers/{server}/database/password` end-to-end. DO NOT
record without explicit user opt-in: this mutation rewrites the password
Forge stores for the database and can break every Forge-managed app on
the server (and Forge's own DB connection) until manually rotated back.
If recording, do it on a throw-away server and pass the prior password
again so nothing changes.

## 3. Redaction
- **Request body** contains a real password under the `password` key.
  Saloon's recorder persists responses only — request bodies are NOT
  written to fixture files — so no redaction layer is required for the
  recorded artifact. Still, never paste real passwords into test code;
  the factory uses a clearly-fake placeholder.
- **Response body**: spec says 204 No Content, so the fixture should be
  empty. A `DatabasePasswordFixture` subclass is unnecessary unless the
  live API surprises us with a body — in which case redact any echoed
  `password` field before committing.

## 4. Spec ambiguities for orchestrator follow-up
- Spec declares `204` (empty body); should we assert empty `->body()`
  in the lifecycle test, or defensively accept a JSON envelope?
- Spec does NOT echo the password back, but Forge has surprised us
  before — log to `docs/FINDINGS.md` if the live response is non-empty.
- `description` explicitly says this does NOT change the password on
  the DB server itself — surface that in the eventual docs page so
  users don't reach for this as a rotation primitive.
