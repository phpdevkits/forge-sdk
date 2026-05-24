<!--
    Logo/banner: drop the image at art/logo.png, then replace the <h1>
    block below with:

    <p align="center">
        <img src="art/logo.png" alt="Forge SDK" width="320">
    </p>
-->
<p align="center">
    <h1 align="center">Forge SDK</h1>
</p>

<p align="center">
    <a href="https://github.com/phpdevkits/forge-sdk/actions"><img alt="Tests" src="https://github.com/phpdevkits/forge-sdk/actions/workflows/tests.yml/badge.svg"></a>
    <a href="https://packagist.org/packages/phpdevkits/forge-sdk"><img alt="Total Downloads" src="https://img.shields.io/packagist/dt/phpdevkits/forge-sdk"></a>
    <a href="https://packagist.org/packages/phpdevkits/forge-sdk"><img alt="Latest Version" src="https://img.shields.io/packagist/v/phpdevkits/forge-sdk"></a>
    <a href="https://packagist.org/packages/phpdevkits/forge-sdk"><img alt="License" src="https://img.shields.io/packagist/l/phpdevkits/forge-sdk"></a>
</p>

------

**Forge SDK** is an ultra-strict, type-safe PHP client for the [Laravel Forge API](https://forge.laravel.com/api-documentation), built on [Saloon v3](https://docs.saloon.dev/). It is engineered for developers who want to automate their Forge infrastructure with the same rigor they apply to their application code — fully typed, immutable, and fail-fast.

## Why This SDK?

The Forge API is JSON:API with cursor pagination, async endpoints, and more than a few places where the documented schema and the live behavior disagree. This SDK absorbs all of that so you don't have to:

- **100% Type Coverage**: Every method, property, and parameter is explicitly typed — no `mixed`, no array soup.
- **Immutable, hydrated DTOs**: Responses become `final readonly` objects (`Server`, `Site`, `Deployment`, …) with typed fields and `DateTimeImmutable` dates, not loose arrays.
- **Fail-fast, typed exceptions**: Every non-2xx response throws a specific exception (`ValidationException`, `NotFoundException`, `RateLimitException`, …) so errors surface at the call site, never silently.
- **Pagination that gets out of your way**: Cursor pagination is wrapped in `Page<T>` with a lazy `iterate()` that walks every page for you.
- **Framework-agnostic**: No Laravel required. It's plain Saloon — drop it into any PHP 8.4+ project.
- **Battle-tested against the real API**: 100% test coverage, with every fixture recorded against live Forge and scrubbed of PII.

Spec-vs-runtime divergences we've hit (and how the SDK handles them) are catalogued in [`docs/FINDINGS.md`](docs/FINDINGS.md).

## Getting Started

> **Requires [PHP 8.4+](https://php.net/releases/).**

Install via [Composer](https://getcomposer.org):

```bash
composer require phpdevkits/forge-sdk
```

Grab a personal access token from your [Forge API settings](https://forge.laravel.com/user-profile/api), then build a `Forge` client one of three ways:

```php
use PhpDevKits\ForgeSdk\Forge;

// 1. Explicit
$forge = new Forge(token: 'your-forge-api-token', organization: 'acme');

// 2. From environment (FORGE_TOKEN, optional FORGE_ORGANIZATION)
$forge = Forge::fromEnvironment();

// 3. From a JSON config file — ./forge.json, or $FORGE_CONFIG_PATH, or an explicit path
$forge = Forge::fromConfig();
```

The `forge.json` shape:

```json
{
    "token": "your-forge-api-token",
    "organization": "acme"
}
```

## Usage

### The authenticated user, organizations, and the provider catalog

```php
$user = $forge->me();                       // User DTO

foreach ($forge->organizations()->iterate() as $organization) {
    echo $organization->slug.PHP_EOL;
}

$providers = $forge->providers()->all();    // Page<Provider>
$regions   = $forge->provider('digitalocean')->regions()->all();
```

### Organization context

Org-scoped resources read the organization from the constructor / env / config, or you can switch context per call with an immutable clone:

```php
$servers = $forge->org('another-org')->servers()->all();   // doesn't mutate $forge
```

Calling an org-scoped resource with no organization set throws `OrganizationNotSetException`.

### Servers

```php
use PhpDevKits\ForgeSdk\Data\CreateServerData;
use PhpDevKits\ForgeSdk\Data\HetznerServerConfig;
use PhpDevKits\ForgeSdk\Enums\{PhpVersion, ServerType, UbuntuVersion};

$server = $forge->servers()->create(new CreateServerData(
    name: 'web-1',
    provider: 'hetzner',
    type: ServerType::App,
    ubuntuVersion: UbuntuVersion::Ubuntu2404,
    phpVersion: PhpVersion::Php84,
    hetzner: new HetznerServerConfig(regionId: 'fsn1', sizeId: 'cax11', networkId: 12345),
));

$forge->server($server->id)->reboot();
$forge->server($server->id)->delete();
```

### Sites & deployments

```php
use PhpDevKits\ForgeSdk\Data\{CreateSiteData, UpdateDeploymentScriptData};
use PhpDevKits\ForgeSdk\Enums\SiteType;

$site = $forge->server($serverId)->sites()->create(new CreateSiteData(
    type: SiteType::Laravel,
    name: 'app',
    domainMode: 'on-forge',
));

$forge->server($serverId)->site($site->id)
    ->deploymentScript()->update(new UpdateDeploymentScriptData(content: $script));

$deployment = $forge->server($serverId)->site($site->id)->deploy();   // Deployment DTO
```

### SSH keys & daemons

```php
use PhpDevKits\ForgeSdk\Data\{CreateSshKeyData, CreateDaemonData};
use PhpDevKits\ForgeSdk\Enums\DaemonUser;

$forge->server($serverId)->sshKeys()->create(new CreateSshKeyData(name: 'laptop', key: $publicKey));

$forge->server($serverId)->daemons()->create(new CreateDaemonData(
    name: 'queue-worker',
    command: 'php artisan queue:work',
    user: DaemonUser::Forge,
));
$forge->server($serverId)->daemon($daemonId)->restart();
```

### Pagination

Every collection exposes `all(?Options)` for a single `Page<T>` and `iterate(?Options)` for a lazy `Generator` across all pages:

```php
$page = $forge->servers()->all(new ListServersOptions(size: 25, provider: 'hetzner'));
if ($page->hasMore()) {
    $next = $forge->servers()->all(new ListServersOptions(cursor: $page->nextCursor));
}

foreach ($forge->servers()->iterate() as $server) {
    // walks every page automatically
}
```

### Testing your own code

It's Saloon underneath, so you can fake Forge in your own suite without touching the network:

```php
use PhpDevKits\ForgeSdk\Forge;
use PhpDevKits\ForgeSdk\Requests\Me\GetMe;
use Saloon\Http\Faking\{MockClient, MockResponse};

$mock = new MockClient([
    GetMe::class => MockResponse::make([
        'data' => [
            'id' => '1',
            'type' => 'users',
            'attributes' => ['name' => 'Test User', 'email' => 'test@example.com'],
            'links' => ['self' => ['href' => 'https://forge.laravel.com/api/user']],
        ],
    ]),
]);

$forge = new Forge('test-token')->withMockClient($mock);
```

> **Tracks the Forge API while it is `v0.x` — minor versions of this SDK may include breaking changes until `1.0`.**

## Development

```bash
composer install
composer test         # full suite: lint, static analysis, type coverage, unit
composer test:unit    # pest only
composer lint         # pint + rector autofix
```

## Contributing

Pull requests are welcome — please open an issue first for anything non-trivial so we can agree on the shape.

## License

**Forge SDK** was created by **[Francisco Barrento](https://github.com/fbarrento)** under the **[MIT license](https://opensource.org/licenses/MIT)**.
