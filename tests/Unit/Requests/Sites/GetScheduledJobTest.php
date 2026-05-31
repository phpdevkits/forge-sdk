<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Sites;

use PhpDevKits\ForgeSdk\Requests\Sites\GetScheduledJob;
use Saloon\Enums\Method;

test('resolveEndpoint() points at a single site-scoped scheduled job',
    function (): void {
        expect(new GetScheduledJob('acme', 42, 7, 13)->resolveEndpoint())->toBe('/orgs/acme/servers/42/sites/7/scheduled-jobs/13');
    });

test('uses the GET method',
    function (): void {
        expect(new GetScheduledJob('acme', 42, 7, 13)->getMethod())->toBe(Method::GET);
    });
