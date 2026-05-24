<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Servers;

use PhpDevKits\ForgeSdk\Requests\Servers\DeleteDaemon;
use Saloon\Enums\Method;

test('resolveEndpoint() interpolates the daemon id',
    function (): void {
        expect(new DeleteDaemon('acme', 42, 7)->resolveEndpoint())->toBe('/orgs/acme/servers/42/background-processes/7');
    });

test('uses the DELETE method',
    function (): void {
        expect(new DeleteDaemon('acme', 42, 7)->getMethod())->toBe(Method::DELETE);
    });
