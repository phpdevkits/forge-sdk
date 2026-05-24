<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Servers;

use PhpDevKits\ForgeSdk\Requests\Servers\GetNginxConfig;
use Saloon\Enums\Method;

test('resolveEndpoint() points at the nginx sub-resource',
    function (): void {
        expect(new GetNginxConfig('acme', 42, 99)->resolveEndpoint())->toBe('/orgs/acme/servers/42/sites/99/nginx');
    });

test('uses the GET method',
    function (): void {
        expect(new GetNginxConfig('acme', 42, 99)->getMethod())->toBe(Method::GET);
    });
