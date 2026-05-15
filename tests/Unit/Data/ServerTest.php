<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use DateTimeImmutable;
use InvalidArgumentException;
use PhpDevKits\ForgeSdk\Data\Server;
use Tests\Factories\ServerFactory;

beforeEach(function (): void {
    $this->factory = new ServerFactory;
});

test('factory produces a usable Server',
    function (): void {
        $server = $this->factory->make();

        expect($server)->toBeInstanceOf(Server::class)
            ->and($server->id)->toBeString()->not->toBeEmpty()
            ->and($server->createdAt)->toBeInstanceOf(DateTimeImmutable::class);
    });

test('::from() hydrates a full JSON:API resource object',
    function (): void {
        $server = Server::from([
            'id' => '42',
            'type' => 'servers',
            'attributes' => [
                'id' => 42,
                'credential_id' => 7,
                'name' => 'production-web-01',
                'slug' => 'production-web-01',
                'type' => 'app',
                'ubuntu_version' => '24.04',
                'ssh_port' => 22,
                'provider' => 'digitalocean',
                'identifier' => '123456',
                'size' => 's-2vcpu-2gb',
                'region' => 'nyc3',
                'php_version' => 'php84',
                'php_cli_version' => 'php84',
                'opcache_status' => 'enabled',
                'database_type' => 'mysql',
                'db_status' => 'installed',
                'redis_status' => 'installed',
                'ip_address' => '10.0.0.1',
                'private_ip_address' => '10.0.0.2',
                'revoked' => false,
                'created_at' => '2026-01-01T00:00:00Z',
                'updated_at' => '2026-05-01T00:00:00Z',
                'connection_status' => 'connected',
                'timezone' => 'UTC',
                'local_public_key' => 'ssh-ed25519 AAAA...',
                'is_ready' => true,
            ],
        ]);

        expect($server->id)->toBe('42')
            ->and($server->credentialId)->toBe(7)
            ->and($server->name)->toBe('production-web-01')
            ->and($server->type)->toBe('app')
            ->and($server->ubuntuVersion)->toBe('24.04')
            ->and($server->sshPort)->toBe(22)
            ->and($server->provider)->toBe('digitalocean')
            ->and($server->phpVersion)->toBe('php84')
            ->and($server->revoked)->toBeFalse()
            ->and($server->isReady)->toBeTrue()
            ->and($server->createdAt->format('Y-m-d'))->toBe('2026-01-01');
    });

test('::from() hydrates with optional fields nulled',
    function (): void {
        $server = Server::from([
            'id' => '1',
            'attributes' => [
                'credential_id' => null,
                'name' => 'x',
                'slug' => 'x',
                'type' => 'app',
                'ubuntu_version' => null,
                'ssh_port' => 22,
                'provider' => 'aws',
                'identifier' => null,
                'size' => 'x',
                'region' => 'x',
                'php_version' => null,
                'php_cli_version' => null,
                'opcache_status' => null,
                'database_type' => null,
                'db_status' => null,
                'redis_status' => null,
                'ip_address' => null,
                'private_ip_address' => null,
                'revoked' => false,
                'created_at' => '2026-01-01T00:00:00Z',
                'updated_at' => '2026-01-02T00:00:00Z',
                'connection_status' => 'pending',
                'timezone' => 'UTC',
                'local_public_key' => null,
                'is_ready' => false,
            ],
        ]);

        expect($server->credentialId)->toBeNull()
            ->and($server->ubuntuVersion)->toBeNull()
            ->and($server->ipAddress)->toBeNull()
            ->and($server->localPublicKey)->toBeNull();
    });

test('::from() throws when id is missing',
    function (): void {
        Server::from(['attributes' => []]);
    })->throws(InvalidArgumentException::class, 'missing the `id` field');

test('::from() throws when attributes is missing',
    function (): void {
        Server::from(['id' => '1']);
    })->throws(InvalidArgumentException::class, 'missing the `attributes` object');

test('::from() throws when a required string field is missing',
    function (): void {
        Server::from(['id' => '1', 'attributes' => []]);
    })->throws(InvalidArgumentException::class, '`attributes.name` must be a string');

test('::from() throws when a required integer field is missing',
    function (): void {
        Server::from(['id' => '1', 'attributes' => [
            'name' => 'x', 'slug' => 'x', 'type' => 'app',
            'provider' => 'aws', 'size' => 'x', 'region' => 'x',
            'connection_status' => 'c', 'timezone' => 'UTC',
            'revoked' => false, 'is_ready' => false,
            'created_at' => '2026-01-01T00:00:00Z',
            'updated_at' => '2026-01-01T00:00:00Z',
        ]]);
    })->throws(InvalidArgumentException::class, '`attributes.ssh_port` must be an integer');

test('::from() throws when a required boolean field is missing',
    function (): void {
        Server::from(['id' => '1', 'attributes' => [
            'name' => 'x', 'slug' => 'x', 'type' => 'app',
            'ssh_port' => 22, 'provider' => 'aws', 'size' => 'x',
            'region' => 'x', 'connection_status' => 'c', 'timezone' => 'UTC',
            'is_ready' => false,
            'created_at' => '2026-01-01T00:00:00Z',
            'updated_at' => '2026-01-01T00:00:00Z',
        ]]);
    })->throws(InvalidArgumentException::class, '`attributes.revoked` must be a boolean');

test('::from() throws when a required date field is missing',
    function (): void {
        Server::from(['id' => '1', 'attributes' => [
            'name' => 'x', 'slug' => 'x', 'type' => 'app',
            'ssh_port' => 22, 'provider' => 'aws', 'size' => 'x',
            'region' => 'x', 'connection_status' => 'c', 'timezone' => 'UTC',
            'revoked' => false, 'is_ready' => false,
        ]]);
    })->throws(InvalidArgumentException::class, '`attributes.created_at` must be a string');

test('::from() throws when a required date field is malformed',
    function (): void {
        Server::from(['id' => '1', 'attributes' => [
            'name' => 'x', 'slug' => 'x', 'type' => 'app',
            'ssh_port' => 22, 'provider' => 'aws', 'size' => 'x',
            'region' => 'x', 'connection_status' => 'c', 'timezone' => 'UTC',
            'revoked' => false, 'is_ready' => false,
            'created_at' => 'not-a-date',
            'updated_at' => '2026-01-01T00:00:00Z',
        ]]);
    })->throws(InvalidArgumentException::class, '`attributes.created_at` is not a valid date-time');

test('::from() throws when an optional string field is the wrong type',
    function (): void {
        Server::from(['id' => '1', 'attributes' => [
            'name' => 'x', 'slug' => 'x', 'type' => 'app',
            'ubuntu_version' => 24.04,
        ]]);
    })->throws(InvalidArgumentException::class, '`attributes.ubuntu_version` must be a string or null');

test('::from() throws when an optional integer field is the wrong type',
    function (): void {
        Server::from(['id' => '1', 'attributes' => [
            'name' => 'x', 'credential_id' => '7',
        ]]);
    })->throws(InvalidArgumentException::class, '`attributes.credential_id` must be an integer or null');

test('jsonSerialize() emits snake_case keys with ATOM-formatted dates',
    function (): void {
        $server = $this->factory->make([
            'id' => '99',
            'createdAt' => new DateTimeImmutable('2026-01-01T00:00:00+00:00'),
            'updatedAt' => new DateTimeImmutable('2026-02-01T00:00:00+00:00'),
        ]);

        $json = $server->jsonSerialize();

        expect($json['id'])->toBe('99')
            ->and($json)->toHaveKey('credential_id')
            ->and($json)->toHaveKey('ssh_port')
            ->and($json)->toHaveKey('ip_address')
            ->and($json)->toHaveKey('is_ready')
            ->and($json['created_at'])->toBe('2026-01-01T00:00:00+00:00')
            ->and($json['updated_at'])->toBe('2026-02-01T00:00:00+00:00');
    });
