# Firewall Rules integration (issue #35)

## 1. Accessors to add to `ServerResource`

```php
public function firewallRules(): FirewallRulesResource
{
    return new FirewallRulesResource($this->connector, $this->organization, $this->id);
}

public function firewallRule(int|string $ruleId): FirewallRuleResource
{
    return new FirewallRuleResource($this->connector, $this->organization, $this->id, $ruleId);
}
```

Mirrors `sshKeys()` / `sshKey($id)`. Not wired here (out of scope: do not edit `ServerResource`).

## 2. Lifecycle tests + fixtures to record (on an existing server)

- `firewall-rules/index` — `all()` returns a `Page<FirewallRule>`.
- `firewall-rules/get` — `firewallRule($id)->get()` hydrates one rule.
- `firewall-rules/create` — `create()` returns void (POST is **202 Accepted, empty body**, async — no `RuleResource`; re-list and match by name).
- `firewall-rules/delete` — `delete()` returns void (also **202 Accepted**).
- Cursor walk for `iterate()` if >1 page.

Use a try/finally lifecycle test: create -> list/find by name -> get -> delete.

## 3. Redaction

`RuleResource` attributes (name, port, type, ip_address, status, dates) are not PII, but `ip_address` can reveal infra. Add a `FirewallRuleFixture` subclass redacting `attributes.ip_address` plus the standard connector-level token/server-IP redaction.

## 4. Spec ambiguities

- `CreateFirewallRuleRequest.ip_address` is typed `object` in the spec (vs. nullable string in `RuleResource`). Treated as an optional string here, matching the response shape and Forge's UI; verify when recording.
- Attribute `type` is a free `string` in `RuleResource` (not the `RuleType` enum), so the DTO keeps it as `string`; `RuleType` (allow/deny) is used only on the create payload.
- POST/DELETE are async (202, empty body) — confirmed against spec, mirrors SSH-key behaviour.
