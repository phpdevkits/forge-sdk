<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Servers;

use PhpDevKits\ForgeSdk\Requests\Servers\GetScheduledJobOutput;
use Saloon\Enums\Method;

test('resolveEndpoint() points at the scheduled job output sub-resource',
    function (): void {
        expect(new GetScheduledJobOutput('acme', 42, 7)->resolveEndpoint())->toBe('/orgs/acme/servers/42/scheduled-jobs/7/output');
    });

test('uses the GET method',
    function (): void {
        expect(new GetScheduledJobOutput('acme', 42, 7)->getMethod())->toBe(Method::GET);
    });
