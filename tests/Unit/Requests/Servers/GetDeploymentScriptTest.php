<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Servers;

use PhpDevKits\ForgeSdk\Requests\Servers\GetDeploymentScript;
use Saloon\Enums\Method;

test('resolveEndpoint() points at the deployments/script sub-resource',
    function (): void {
        expect(new GetDeploymentScript('acme', 42, 99)->resolveEndpoint())->toBe('/orgs/acme/servers/42/sites/99/deployments/script');
    });

test('uses the GET method',
    function (): void {
        expect(new GetDeploymentScript('acme', 42, 99)->getMethod())->toBe(Method::GET);
    });
