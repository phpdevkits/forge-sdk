<?php

declare(strict_types=1);

use PhpDevKits\ForgeSdk\ForgeConnector;
use PhpDevKits\ForgeSdk\Requests\Servers\GetServerRequest;
use PhpDevKits\ForgeSdk\Requests\Servers\GetServersRequest;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

it('targets the latest Forge API base URL', function (): void {
    $connector = new ForgeConnector('test-token');

    expect($connector->resolveBaseUrl())->toBe('https://forge.laravel.com/api/v1');
});

it('lists servers and authenticates with a bearer token', function (): void {
    $mock = new MockClient([
        GetServersRequest::class => MockResponse::make([
            'servers' => [
                ['id' => 1, 'name' => 'web-1'],
            ],
        ]),
    ]);

    $connector = new ForgeConnector('test-token');
    $connector->withMockClient($mock);

    $response = $connector->send(new GetServersRequest);

    expect($response->status())->toBe(200);
    expect($response->json('servers.0.name'))->toBe('web-1');

    $headers = $response->getPendingRequest()->headers();

    expect($headers->get('Authorization'))->toBe('Bearer test-token');
    expect($headers->get('Accept'))->toBe('application/json');
    expect($headers->get('Content-Type'))->toBe('application/json');
});

it('targets a single server by id', function (): void {
    $mock = new MockClient([
        GetServerRequest::class => MockResponse::make([
            'server' => ['id' => 42, 'name' => 'db-1'],
        ]),
    ]);

    $connector = new ForgeConnector('test-token');
    $connector->withMockClient($mock);

    $request = new GetServerRequest(42);

    expect($request->resolveEndpoint())->toBe('/servers/42');

    $response = $connector->send($request);

    expect($response->json('server.id'))->toBe(42);
});
