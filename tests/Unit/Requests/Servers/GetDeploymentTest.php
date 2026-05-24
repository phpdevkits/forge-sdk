<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Servers;

use PhpDevKits\ForgeSdk\Requests\Servers\GetDeployment;
use Saloon\Enums\Method;

test('resolveEndpoint() interpolates the full deployment path',
    function (): void {
        expect(new GetDeployment('acme', 42, 99, 7)->resolveEndpoint())->toBe('/orgs/acme/servers/42/sites/99/deployments/7');
    });

test('uses the GET method',
    function (): void {
        expect(new GetDeployment('acme', 42, 99, 7)->getMethod())->toBe(Method::GET);
    });
