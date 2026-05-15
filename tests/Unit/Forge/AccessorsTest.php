<?php

declare(strict_types=1);

namespace Tests\Unit\Forge;

use PhpDevKits\ForgeSdk\Exceptions\OrganizationNotSetException;
use PhpDevKits\ForgeSdk\Forge;
use PhpDevKits\ForgeSdk\Resources\OrganizationResource;
use PhpDevKits\ForgeSdk\Resources\OrganizationsResource;
use PhpDevKits\ForgeSdk\Resources\ProviderResource;
use PhpDevKits\ForgeSdk\Resources\ProvidersResource;
use PhpDevKits\ForgeSdk\Resources\ServerResource;
use PhpDevKits\ForgeSdk\Resources\ServersResource;
use Saloon\Http\Faking\MockClient;

test('organizations() returns an OrganizationsResource',
    function (): void {
        $forge = new Forge('test-token');

        expect($forge->organizations())->toBeInstanceOf(OrganizationsResource::class);
    });

test('organization($slug) returns an OrganizationResource',
    function (): void {
        $forge = new Forge('test-token');

        expect($forge->organization('acme'))->toBeInstanceOf(OrganizationResource::class);
    });

test('providers() returns a ProvidersResource',
    function (): void {
        $forge = new Forge('test-token');

        expect($forge->providers())->toBeInstanceOf(ProvidersResource::class);
    });

test('provider($id) returns a ProviderResource',
    function (): void {
        $forge = new Forge('test-token');

        expect($forge->provider('digitalocean'))->toBeInstanceOf(ProviderResource::class);
    });

test('org($slug) returns a clone bound to the given organization',
    function (): void {
        $original = new Forge('test-token', 'acme');

        $chained = $original->org('other-org');

        expect($chained)->toBeInstanceOf(Forge::class)
            ->and($chained->defaultOrganization)->toBe('other-org')
            ->and($original->defaultOrganization)->toBe('acme');
    });

test('org($slug) leaves the original Forge instance untouched',
    function (): void {
        $original = new Forge('test-token');

        $chained = $original->org('other-org');

        expect($original->defaultOrganization)->toBeNull()
            ->and($chained->defaultOrganization)->toBe('other-org');
    });

test('org($slug) carries an attached MockClient over to the clone',
    function (): void {
        $original = new Forge('test-token');
        $mockClient = new MockClient([]);
        $original->withMockClient($mockClient);

        $chained = $original->org('other-org');

        expect($chained->getMockClient())->toBe($mockClient);
    });

test('servers() returns a ServersResource when an organization is set',
    function (): void {
        $forge = new Forge('test-token', 'acme');

        expect($forge->servers())->toBeInstanceOf(ServersResource::class);
    });

test('server($id) returns a ServerResource when an organization is set',
    function (): void {
        $forge = new Forge('test-token', 'acme');

        expect($forge->server(42))->toBeInstanceOf(ServerResource::class);
    });

test('servers() throws OrganizationNotSetException when no organization is bound',
    function (): void {
        new Forge('test-token')->servers();
    })->throws(OrganizationNotSetException::class, 'Forge::servers requires an organization');

test('server($id) throws OrganizationNotSetException when no organization is bound',
    function (): void {
        new Forge('test-token')->server(1);
    })->throws(OrganizationNotSetException::class, 'Forge::server requires an organization');
