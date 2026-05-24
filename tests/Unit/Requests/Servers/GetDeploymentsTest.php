<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Servers;

use PhpDevKits\ForgeSdk\Data\ListDeploymentsOptions;
use PhpDevKits\ForgeSdk\Requests\Servers\GetDeployments;
use Saloon\Enums\Method;

test('resolveEndpoint() interpolates the 4-level org/server/site path',
    function (): void {
        expect(new GetDeployments('acme', 42, 99)->resolveEndpoint())->toBe('/orgs/acme/servers/42/sites/99/deployments');
    });

test('uses the GET method',
    function (): void {
        expect(new GetDeployments('acme', 42, 99)->getMethod())->toBe(Method::GET);
    });

test('emits options as query parameters',
    function (): void {
        $request = new GetDeployments('acme', 42, 99, new ListDeploymentsOptions(size: 5, commitAuthor: 'jane'));

        expect($request->query()->all())->toBe([
            'page[size]' => 5,
            'filter[commit_author]' => 'jane',
        ]);
    });
