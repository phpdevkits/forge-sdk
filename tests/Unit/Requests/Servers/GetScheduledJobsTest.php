<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Servers;

use PhpDevKits\ForgeSdk\Data\ListScheduledJobsOptions;
use PhpDevKits\ForgeSdk\Requests\Servers\GetScheduledJobs;
use Saloon\Enums\Method;

test('resolveEndpoint() points at the scheduled-jobs collection',
    function (): void {
        expect(new GetScheduledJobs('acme', 42)->resolveEndpoint())->toBe('/orgs/acme/servers/42/scheduled-jobs');
    });

test('uses the GET method',
    function (): void {
        expect(new GetScheduledJobs('acme', 42)->getMethod())->toBe(Method::GET);
    });

test('emits options as query parameters',
    function (): void {
        $request = new GetScheduledJobs('acme', 42, new ListScheduledJobsOptions(size: 5, status: 'installed'));

        expect($request->query()->all())->toBe([
            'page[size]' => 5,
            'filter[status]' => 'installed',
        ]);
    });
