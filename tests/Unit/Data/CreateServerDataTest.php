<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use PhpDevKits\ForgeSdk\Data\CreateServerData;
use PhpDevKits\ForgeSdk\Data\HetznerServerConfig;
use PhpDevKits\ForgeSdk\Enums\DatabaseType;
use PhpDevKits\ForgeSdk\Enums\PhpVersion;
use PhpDevKits\ForgeSdk\Enums\ServerType;
use PhpDevKits\ForgeSdk\Enums\UbuntuVersion;
use Tests\Factories\CreateServerDataFactory;

beforeEach(function (): void {
    $this->factory = new CreateServerDataFactory;
});

test('factory produces a usable CreateServerData',
    function (): void {
        $data = $this->factory->make();

        expect($data)->toBeInstanceOf(CreateServerData::class)
            ->and($data->name)->toBeString()->not->toBeEmpty();
    });

test('toArray() emits the minimum required payload with enums serialized',
    function (): void {
        $data = new CreateServerData(
            name: 'production-web-01',
            provider: 'hetzner',
            type: ServerType::App,
            ubuntuVersion: UbuntuVersion::Ubuntu2404,
        );

        expect($data->toArray())->toBe([
            'name' => 'production-web-01',
            'provider' => 'hetzner',
            'type' => 'app',
            'ubuntu_version' => '24.04',
        ]);
    });

test('toArray() emits credential_id and php_version when set',
    function (): void {
        $data = new CreateServerData(
            name: 'production-web-01',
            provider: 'hetzner',
            type: ServerType::App,
            ubuntuVersion: UbuntuVersion::Ubuntu2404,
            credentialId: 7,
            phpVersion: PhpVersion::Php84,
        );

        $payload = $data->toArray();

        expect($payload['credential_id'])->toBe(7)
            ->and($payload['php_version'])->toBe('php84');
    });

test('toArray() emits database_type when set',
    function (): void {
        $data = new CreateServerData(
            name: 'x',
            provider: 'hetzner',
            type: ServerType::Database,
            ubuntuVersion: UbuntuVersion::Ubuntu2404,
            databaseType: DatabaseType::Postgres17,
        );

        expect($data->toArray()['database_type'])->toBe('postgres17');
    });

test('toArray() embeds the Hetzner config under `hetzner`',
    function (): void {
        $data = new CreateServerData(
            name: 'x',
            provider: 'hetzner',
            type: ServerType::App,
            ubuntuVersion: UbuntuVersion::Ubuntu2404,
            hetzner: new HetznerServerConfig(regionId: 'fsn1', sizeId: 'cpx11'),
        );

        expect($data->toArray()['hetzner'])->toBe([
            'region_id' => 'fsn1',
            'size_id' => 'cpx11',
        ]);
    });
