# Integration notes — issue #33 (Server Credentials + VPCs)

This slice ships production classes + factories + credential-free unit tests
only. The orchestrator must wire accessors and record the fixture-backed
lifecycle tests. No shared files were edited.

## 1. Accessor methods to wire (on `src/Forge.php`)

Server credentials are **org-scoped** (top-level under `/orgs/{org}`), so the
accessors use `requireOrganization()` exactly like `servers()` / `server()`:

```php
public function serverCredentials(): ServerCredentialsResource
{
    return new ServerCredentialsResource($this, $this->requireOrganization('serverCredentials'));
}

public function serverCredential(int|string $id): ServerCredentialResource
{
    return new ServerCredentialResource($this, $this->requireOrganization('serverCredential'), $id);
}
```

Add the matching `use` imports:

```php
use PhpDevKits\ForgeSdk\Resources\ServerCredentialResource;
use PhpDevKits\ForgeSdk\Resources\ServerCredentialsResource;
```

VPCs are reached by chaining (no Forge accessor needed):

```php
$forge->serverCredential($id)->region('us-east-1')->all();        // VpcsResource::all()
$forge->serverCredential($id)->region('us-east-1')->iterate();    // VpcsResource::iterate()
$forge->serverCredential($id)->region('us-east-1')->create($data);// VpcsResource::create()
$forge->serverCredential($id)->region('us-east-1')->vpc('vpc-1')->get(); // VpcResource::get()
```

`ServerCredentialResource::region(string $r)` returns a `VpcsResource`;
`VpcsResource::vpc(int|string $id)` returns a `VpcResource`.

## 2. Resource lifecycle tests + fixtures to record

Record against the live API (orchestrator only — this slice has no creds). All
responses are JSON:API (`application/vnd.api+json`). Suggested fixture names:

| Request | Endpoint | Fixture |
|---|---|---|
| `GetServerCredentials` | `GET /orgs/{org}/server-credentials` | `server-credentials/list` |
| `GetServerCredential` | `GET /orgs/{org}/server-credentials/{credential}` | `server-credentials/get` |
| `GetVpcs` | `GET /orgs/{org}/server-credentials/{credential}/regions/{region}/vpcs` | `server-credentials/vpcs/list` |
| `GetVpc` | `GET .../vpcs/{vpcId}` | `server-credentials/vpcs/get` |
| `CreateVpc` | `POST .../vpcs` | `server-credentials/vpcs/create` |

Lifecycle tests to add (mirror `ServersResource` / `SshKeysResource` tests):

- `ServerCredentialsResource::all()` hydrates a `Page<ServerCredential>`;
  `iterate()` walks cursors (only `next_cursor`/`prev_cursor` in meta).
- `ServerCredentialResource::get()` hydrates one `ServerCredential`.
- `VpcsResource::all()` hydrates a `Page<Vpc>` incl. nested `subnets`.
- `VpcResource::get()` hydrates one `Vpc`.
- `VpcsResource::create()` returns the hydrated `Vpc` from the 201 body.

## 3. Fixture redaction

Server credentials carry no obvious PII, but `name`/`provider` may leak
account-identifying info, and VPC `cidrBlock`/`subnets[*].cidrBlock` /
`id`/`region` reveal real network topology. Recommend a
`ServerCredentialFixture` (and/or `VpcFixture`) subclass under `tests/Utils/`
that redacts:

- credential `attributes.name` (account-named), `attributes.provider` is fine.
- vpc `id`, `attributes.cidrBlock`, `attributes.region`,
  `attributes.subnets[*].id`, `attributes.subnets[*].cidrBlock`.
- any `links.self.href` that embeds the org slug / real ids.

No fixtures were written in this slice.

## 4. Spec ambiguities / runtime risks

- **VPC create response**: the spec lists BOTH a `201` (→ `VpcResource`) and a
  bare `200` (`{type: object}`, empty). `VpcsResource::create()` assumes the
  `201` path and hydrates `data` into a `Vpc`. If Forge actually returns 200
  with an empty/async body (cf. SSH-key create returning 202), change
  `create()` to `void` + a note, like `SshKeysResource`. **Verify on record.**
- **`credential` path param is `integer` in the spec** but ids arrive as
  strings in JSON:API `data.id`. Requests accept `int|string` to cover both.
- **VPCs list pagination is page-based** (`current_page`/`per_page`/`from`/`to`
  in meta — no `next_cursor`). `ParsesPage` keys off `next_cursor`, so
  `hasMore()` is always false and `iterate()` yields a single page. If Forge
  returns more than one page of VPCs this will silently truncate — the
  orchestrator may need a page-based pager variant. There are **no documented
  query params** for the VPCs list, so no list-options DTO was created.
- **`ServerCredentialResource` has no `delete()`**: the delete endpoint in the
  spec lives under the team path (`/orgs/{org}/teams/{team}/server-credentials/
  {credential}`), which is out of scope for issue #33.
- `created_at`/`updated_at` on a credential are nullable in the spec; the DTO
  models them as `?DateTimeImmutable`.

## Files added

DTOs: `src/Data/{ServerCredential,Vpc,VpcSubnet,CreateVpcData,ListServerCredentialsOptions}.php`
Requests: `src/Requests/ServerCredentials/{GetServerCredentials,GetServerCredential,GetVpcs,GetVpc,CreateVpc}.php`
Resources: `src/Resources/{ServerCredentialsResource,ServerCredentialResource,VpcsResource,VpcResource}.php`
Factories: `tests/Factories/{ServerCredentialFactory,VpcFactory,VpcSubnetFactory,CreateVpcDataFactory}.php`
Tests: `tests/Unit/Data/{ServerCredentialTest,VpcTest,VpcSubnetTest,CreateVpcDataTest,ListServerCredentialsOptionsTest}.php`,
`tests/Unit/Requests/ServerCredentials/{GetServerCredentials,GetServerCredential,GetVpcs,GetVpc,CreateVpc}Test.php`
