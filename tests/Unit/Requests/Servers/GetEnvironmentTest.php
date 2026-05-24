<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Servers;

use PhpDevKits\ForgeSdk\Requests\Servers\GetEnvironment;
use Saloon\Enums\Method;

test('resolveEndpoint() points at the environment sub-resource',
    function (): void {
        expect(new GetEnvironment('acme', 42, 99)->resolveEndpoint())->toBe('/orgs/acme/servers/42/sites/99/environment');
    });

test('uses the GET method',
    function (): void {
        expect(new GetEnvironment('acme', 42, 99)->getMethod())->toBe(Method::GET);
    });
