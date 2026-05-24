<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Servers;

use PhpDevKits\ForgeSdk\Requests\Servers\GetSite;
use Saloon\Enums\Method;

test('resolveEndpoint() uses the server-less org/sites path',
    function (): void {
        expect(new GetSite('acme', 99)->resolveEndpoint())->toBe('/orgs/acme/sites/99');
    });

test('uses the GET method',
    function (): void {
        expect(new GetSite('acme', 99)->getMethod())->toBe(Method::GET);
    });
