<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Sites;

use PhpDevKits\ForgeSdk\Requests\Sites\GetRedirectRule;
use Saloon\Enums\Method;

test('resolveEndpoint() interpolates the org slug, server id, site id and rule id',
    function (): void {
        expect(new GetRedirectRule('acme', 42, 7, 99)->resolveEndpoint())->toBe('/orgs/acme/servers/42/sites/7/redirect-rules/99');
    });

test('uses the GET method',
    function (): void {
        expect(new GetRedirectRule('acme', 42, 7, 99)->getMethod())->toBe(Method::GET);
    });
