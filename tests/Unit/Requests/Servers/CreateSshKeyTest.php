<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Servers;

use PhpDevKits\ForgeSdk\Data\CreateSshKeyData;
use PhpDevKits\ForgeSdk\Requests\Servers\CreateSshKey;
use Saloon\Enums\Method;

test('resolveEndpoint() interpolates the org slug and server id',
    function (): void {
        $data = new CreateSshKeyData(name: 'x', key: 'ssh-ed25519 ...');

        expect(new CreateSshKey('acme', 42, $data)->resolveEndpoint())->toBe('/orgs/acme/servers/42/ssh-keys');
    });

test('uses the POST method',
    function (): void {
        expect(new CreateSshKey('acme', 42, new CreateSshKeyData(name: 'x', key: 'k'))->getMethod())->toBe(Method::POST);
    });

test('body() reflects the CreateSshKeyData payload',
    function (): void {
        $data = new CreateSshKeyData(name: 'oliver', key: 'ssh-ed25519 KEY', user: 'deploy');

        expect(new CreateSshKey('acme', 42, $data)->body()->all())->toBe([
            'name' => 'oliver',
            'key' => 'ssh-ed25519 KEY',
            'user' => 'deploy',
        ]);
    });
