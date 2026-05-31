<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Sites;

use PhpDevKits\ForgeSdk\Data\ListScheduledJobsOptions;
use PhpDevKits\ForgeSdk\Requests\Sites\GetScheduledJobs;
use Saloon\Enums\Method;

test('resolveEndpoint() interpolates the org slug, server id and site id',
    function (): void {
        $request = new GetScheduledJobs('acme', 42, 7);

        expect($request->resolveEndpoint())->toBe('/orgs/acme/servers/42/sites/7/scheduled-jobs');
    });

test('uses the GET method',
    function (): void {
        expect(new GetScheduledJobs('acme', 42, 7)->getMethod())->toBe(Method::GET);
    });

test('emits options as query parameters when supplied',
    function (): void {
        $request = new GetScheduledJobs('acme', 42, 7, new ListScheduledJobsOptions(size: 5, status: 'installed'));

        expect($request->query()->all())->toBe([
            'page[size]' => 5,
            'filter[status]' => 'installed',
        ]);
    });
