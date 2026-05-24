<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Servers;

use PhpDevKits\ForgeSdk\Data\UpdateDaemonData;
use PhpDevKits\ForgeSdk\Requests\Servers\UpdateDaemon;
use Saloon\Enums\Method;

test('resolveEndpoint() interpolates the daemon id',
    function (): void {
        expect(new UpdateDaemon('acme', 42, 7, new UpdateDaemonData(name: 'w'))->resolveEndpoint())
            ->toBe('/orgs/acme/servers/42/background-processes/7');
    });

test('uses the PUT method',
    function (): void {
        expect(new UpdateDaemon('acme', 42, 7, new UpdateDaemonData(name: 'w'))->getMethod())->toBe(Method::PUT);
    });

test('body() reflects the UpdateDaemonData payload',
    function (): void {
        expect(new UpdateDaemon('acme', 42, 7, new UpdateDaemonData(name: 'worker', config: '[program:x]'))->body()->all())->toBe([
            'name' => 'worker',
            'config' => '[program:x]',
        ]);
    });
