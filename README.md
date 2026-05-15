<p align="center">
    <h1 align="center">Forge SDK</h1>
    <p align="center">
        <a href="https://github.com/phpdevkits/forge-sdk/actions"><img alt="Tests" src="https://github.com/phpdevkits/forge-sdk/actions/workflows/tests.yml/badge.svg"></a>
        <a href="https://packagist.org/packages/phpdevkits/forge-sdk"><img alt="Total Downloads" src="https://img.shields.io/packagist/dt/phpdevkits/forge-sdk"></a>
        <a href="https://packagist.org/packages/phpdevkits/forge-sdk"><img alt="Latest Version" src="https://img.shields.io/packagist/v/phpdevkits/forge-sdk"></a>
        <a href="https://packagist.org/packages/phpdevkits/forge-sdk"><img alt="License" src="https://img.shields.io/packagist/l/phpdevkits/forge-sdk"></a>
    </p>
</p>

------

A modern PHP SDK for the [Laravel Forge API](https://forge.laravel.com/api-documentation), built on top of [Saloon v3](https://docs.saloon.dev/).

> **Requires [PHP 8.4+](https://php.net/releases/)**

## Installation

```bash
composer require phpdevkits/forge-sdk
```

## Usage

Grab a personal access token from your [Forge API settings](https://forge.laravel.com/user-profile/api), then build a `Forge` instance one of three ways:

```php
use PhpDevKits\ForgeSdk\Forge;

// 1. Explicit
$forge = new Forge(token: 'your-forge-api-token', organization: 'acme');

// 2. From environment variables (FORGE_TOKEN, optional FORGE_ORGANIZATION)
$forge = Forge::fromEnvironment();

// 3. From a JSON config file
$forge = Forge::fromConfig('/path/to/forge.json');
// or with no argument: reads ./forge.json (or $FORGE_CONFIG_PATH if set)
$forge = Forge::fromConfig();
```

The `forge.json` shape is:

```json
{
    "token": "your-forge-api-token",
    "organization": "acme"
}
```

### Get the authenticated user

```php
$user = $forge->me();

echo $user->name;   // "Ada Lovelace"
echo $user->email;  // "ada@example.com"
echo $user->id;     // "216174"
```

### List organizations

```php
foreach ($forge->organizations()->iterate() as $organization) {
    echo $organization->slug.PHP_EOL;
}

// Or grab a single page:
$page = $forge->organizations()->all(new ListOrganizationsOptions(size: 10));
foreach ($page as $organization) {
    // ...
}
if ($page->hasMore()) {
    $next = $forge->organizations()->all(new ListOrganizationsOptions(cursor: $page->nextCursor));
}
```

### Get one organization

```php
$organization = $forge->organization('acme')->get();

echo $organization->name;
echo $organization->slug;
```

### Testing your own code

The SDK is built on Saloon, so you can fake the Forge API in your own test suite without hitting the network:

```php
use PhpDevKits\ForgeSdk\Forge;
use PhpDevKits\ForgeSdk\Requests\Me\GetMeRequest;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

$mock = new MockClient([
    GetMeRequest::class => MockResponse::make([
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

> **Tracks Forge API v0.x — minor versions of this SDK may break until 1.0.**

## Development

```bash
composer install
composer test         # full suite: lint, types, type coverage, unit
composer test:unit    # pest only
composer lint         # pint + rector autofix
```

## Contributing

Pull requests are welcome — please open an issue first for anything non-trivial so we can talk through the shape.

## Credits

- [Francisco Barrento](https://github.com/fbarrento)
- [All contributors](https://github.com/phpdevkits/forge-sdk/contributors)

Scaffolded from [nunomaduro/skeleton-php](https://github.com/nunomaduro/skeleton-php).

## License

The MIT License (MIT). See [LICENSE.md](LICENSE.md) for details.
