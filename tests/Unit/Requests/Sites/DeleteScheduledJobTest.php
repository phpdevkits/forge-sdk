<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Sites;

use PhpDevKits\ForgeSdk\Requests\Sites\DeleteScheduledJob;
use Saloon\Enums\Method;

test('resolveEndpoint() points at a single site-scoped scheduled job',
    function (): void {
        expect(new DeleteScheduledJob('acme', 42, 7, 13)->resolveEndpoint())->toBe('/orgs/acme/servers/42/sites/7/scheduled-jobs/13');
    });

test('uses the DELETE method',
    function (): void {
        expect(new DeleteScheduledJob('acme', 42, 7, 13)->getMethod())->toBe(Method::DELETE);
    });
