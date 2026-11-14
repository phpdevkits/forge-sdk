# Site Redirect Rules — Integration Notes (issue #41)

## 1. Accessor wiring (`SiteResource` — NOT edited here)

Add to `src/Resources/SiteResource.php`, mirroring `deployments()`/`deployment()`:

```php
public function redirectRules(): RedirectRulesResource
{
    return new RedirectRulesResource($this->connector, $this->organization, $this->serverId, $this->siteId);
}

public function redirectRule(int|string $redirectRuleId): RedirectRuleResource
{
    return new RedirectRuleResource($this->connector, $this->organization, $this->serverId, $this->siteId, $redirectRuleId);
}
```

Both resource constructors already take `(Connector, organization, serverId, siteId[, redirectRuleId])`.

## 2. Lifecycle tests + fixtures to record (site 3210369)

Fixtures NOT recorded here (no credentials). Record against site `3210369`:

- `redirect-rules/list` — `GET .../redirect-rules` → 200 paginated.
- `redirect-rules/create` — `POST .../redirect-rules` → 202 empty body.
- `redirect-rules/get` — `GET .../redirect-rules/{rule}` → 200 single.
- `redirect-rules/delete` — `DELETE .../redirect-rules/{rule}` → 202 empty body.

Lifecycle: create (202) → poll `all()`/`iterate()`, match by `from`/`to`, wait `status` `installing`→`installed` → `get()` → `delete()` (202) → confirm gone.

## 3. Redaction

`RedirectRuleResource` carries no secrets/PII (`id`, `from`, `to`, `type`, `status`, timestamps are non-sensitive paths). Still scrub the `Authorization` header and any `links.self` host in recorded fixtures, per the standard fixture redaction pass.

## 4. Spec ambiguities

- **Async create → void.** `POST` is `x-processingMode: async`, documented `202 Accepted` with NO response schema. `create()` returns `void` (same call as SSH-key create). Caller must re-list and match by `from`/`to` to get the hydrated DTO. Confirm against a live fixture whether the body is truly empty.
- **Async delete → void.** `DELETE` is also async `202`; `delete()` returns `void`.
- **Attributes not top-level required.** Resource schema requires only `id`/`type`/`links` at top level; `from`/`to`/`type`/`status`/timestamps live under (required) `attributes`. DTO requires `id` + `attributes`, then each attribute — same defensive shape as `SshKey`.
- **`type`/`status` kept as strings on the DTO.** `RedirectType` enum (`redirect`/`permanent`) is used only on the *input* `CreateRedirectRuleData`, matching how `SiteType`/`WwwRedirectType` are input-only. Response `status` (`installing`/`installed`/`removing`) is left as a plain string (no closed-set enum minted for it, consistent with `SiteStatus`-style response fields staying string on DTOs).
