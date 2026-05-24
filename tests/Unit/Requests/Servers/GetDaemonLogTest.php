<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Servers;

use PhpDevKits\ForgeSdk\Requests\Servers\GetDaemonLog;
use Saloon\Enums\Method;

test('resolveEndpoint() points at the daemon log sub-resource',
    function (): void {
        expect(new GetDaemonLog('acme', 42, 7)->resolveEndpoint())->toBe('/orgs/acme/servers/42/background-processes/7/log');
    });

test('uses the GET method',
    function (): void {
        expect(new GetDaemonLog('acme', 42, 7)->getMethod())->toBe(Method::GET);
    });
