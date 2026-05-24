<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Servers;

use PhpDevKits\ForgeSdk\Requests\Servers\GetScheduledJob;
use Saloon\Enums\Method;

test('resolveEndpoint() points at a single scheduled job',
    function (): void {
        expect(new GetScheduledJob('acme', 42, 7)->resolveEndpoint())->toBe('/orgs/acme/servers/42/scheduled-jobs/7');
    });

test('uses the GET method',
    function (): void {
        expect(new GetScheduledJob('acme', 42, 7)->getMethod())->toBe(Method::GET);
    });
