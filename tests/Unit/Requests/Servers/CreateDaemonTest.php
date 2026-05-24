<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Servers;

use PhpDevKits\ForgeSdk\Data\CreateDaemonData;
use PhpDevKits\ForgeSdk\Enums\DaemonUser;
use PhpDevKits\ForgeSdk\Requests\Servers\CreateDaemon;
use Saloon\Enums\Method;

test('resolveEndpoint() posts to the background-processes collection',
    function (): void {
        $data = new CreateDaemonData(name: 'w', command: 'sleep 1', user: DaemonUser::Forge);

        expect(new CreateDaemon('acme', 42, $data)->resolveEndpoint())->toBe('/orgs/acme/servers/42/background-processes');
    });

test('uses the POST method',
    function (): void {
        $data = new CreateDaemonData(name: 'w', command: 'sleep 1', user: DaemonUser::Forge);

        expect(new CreateDaemon('acme', 42, $data)->getMethod())->toBe(Method::POST);
    });

test('body() reflects the CreateDaemonData payload',
    function (): void {
        $data = new CreateDaemonData(name: 'worker', command: 'sleep 9999', user: DaemonUser::Forge);

        expect(new CreateDaemon('acme', 42, $data)->body()->all())->toBe([
            'name' => 'worker',
            'command' => 'sleep 9999',
            'user' => 'forge',
            'processes' => 1,
        ]);
    });
