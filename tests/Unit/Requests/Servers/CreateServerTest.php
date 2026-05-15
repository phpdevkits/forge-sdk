<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Servers;

use PhpDevKits\ForgeSdk\Data\CreateServerData;
use PhpDevKits\ForgeSdk\Enums\ServerType;
use PhpDevKits\ForgeSdk\Enums\UbuntuVersion;
use PhpDevKits\ForgeSdk\Requests\Servers\CreateServer;
use Saloon\Enums\Method;

test('resolveEndpoint() interpolates the organization slug',
    function (): void {
        $data = new CreateServerData(
            name: 'x',
            provider: 'hetzner',
            type: ServerType::App,
            ubuntuVersion: UbuntuVersion::Ubuntu2404,
        );

        $request = new CreateServer('acme', $data);

        expect($request->resolveEndpoint())->toBe('/orgs/acme/servers');
    });

test('uses the POST method',
    function (): void {
        $data = new CreateServerData(
            name: 'x',
            provider: 'hetzner',
            type: ServerType::App,
            ubuntuVersion: UbuntuVersion::Ubuntu2404,
        );

        $request = new CreateServer('acme', $data);

        expect($request->getMethod())->toBe(Method::POST);
    });

test('body() returns the CreateServerData payload as JSON',
    function (): void {
        $data = new CreateServerData(
            name: 'production-web-01',
            provider: 'hetzner',
            type: ServerType::App,
            ubuntuVersion: UbuntuVersion::Ubuntu2404,
        );

        $request = new CreateServer('acme', $data);

        expect($request->body()->all())->toBe([
            'name' => 'production-web-01',
            'provider' => 'hetzner',
            'type' => 'app',
            'ubuntu_version' => '24.04',
        ]);
    });
