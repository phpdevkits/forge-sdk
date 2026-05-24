<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Servers;

use PhpDevKits\ForgeSdk\Requests\Servers\DeleteSite;
use Saloon\Enums\Method;

test('resolveEndpoint() interpolates org slug, server id and site id',
    function (): void {
        expect(new DeleteSite('acme', 42, 99)->resolveEndpoint())->toBe('/orgs/acme/servers/42/sites/99');
    });

test('uses the DELETE method',
    function (): void {
        expect(new DeleteSite('acme', 42, 99)->getMethod())->toBe(Method::DELETE);
    });
