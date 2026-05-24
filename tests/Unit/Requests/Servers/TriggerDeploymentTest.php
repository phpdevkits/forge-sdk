<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Servers;

use PhpDevKits\ForgeSdk\Requests\Servers\TriggerDeployment;
use Saloon\Enums\Method;

test('resolveEndpoint() posts to the deployments collection',
    function (): void {
        expect(new TriggerDeployment('acme', 42, 99)->resolveEndpoint())->toBe('/orgs/acme/servers/42/sites/99/deployments');
    });

test('uses the POST method',
    function (): void {
        expect(new TriggerDeployment('acme', 42, 99)->getMethod())->toBe(Method::POST);
    });
