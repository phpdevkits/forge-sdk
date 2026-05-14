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

Grab a personal access token from your [Forge API settings](https://forge.laravel.com/user-profile/api), then point the connector at it:

```php
use PhpDevKits\ForgeSdk\ForgeConnector;
use PhpDevKits\ForgeSdk\Requests\Servers\GetServersRequest;

$forge = new ForgeConnector(token: 'your-forge-api-token');

$response = $forge->send(new GetServersRequest);

$servers = $response->json('servers');
```

### Fetch a single server

```php
use PhpDevKits\ForgeSdk\Requests\Servers\GetServerRequest;

$response = $forge->send(new GetServerRequest(serverId: 42));

$server = $response->json('server');
```

### Testing

The SDK is built on Saloon, so you can fake the Forge API in your own test suite without hitting the network:

```php
use PhpDevKits\ForgeSdk\ForgeConnector;
use PhpDevKits\ForgeSdk\Requests\Servers\GetServersRequest;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

$mock = new MockClient([
    GetServersRequest::class => MockResponse::make(['servers' => []]),
]);

$forge = (new ForgeConnector('test-token'))->withMockClient($mock);
```

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
