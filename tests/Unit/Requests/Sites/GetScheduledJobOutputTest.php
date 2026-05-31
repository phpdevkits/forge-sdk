<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Sites;

use PhpDevKits\ForgeSdk\Requests\Sites\GetScheduledJobOutput;
use Saloon\Enums\Method;

test('resolveEndpoint() points at the site-scoped scheduled job output sub-resource',
    function (): void {
        expect(new GetScheduledJobOutput('acme', 42, 7, 13)->resolveEndpoint())->toBe('/orgs/acme/servers/42/sites/7/scheduled-jobs/13/output');
    });

test('uses the GET method',
    function (): void {
        expect(new GetScheduledJobOutput('acme', 42, 7, 13)->getMethod())->toBe(Method::GET);
    });
