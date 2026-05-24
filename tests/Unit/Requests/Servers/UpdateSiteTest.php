<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Servers;

use PhpDevKits\ForgeSdk\Data\UpdateSiteData;
use PhpDevKits\ForgeSdk\Requests\Servers\UpdateSite;
use Saloon\Enums\Method;

test('resolveEndpoint() interpolates org slug, server id and site id',
    function (): void {
        expect(new UpdateSite('acme', 42, 99, new UpdateSiteData)->resolveEndpoint())->toBe('/orgs/acme/servers/42/sites/99');
    });

test('uses the PUT method',
    function (): void {
        expect(new UpdateSite('acme', 42, 99, new UpdateSiteData)->getMethod())->toBe(Method::PUT);
    });

test('body() reflects only the fields that are set',
    function (): void {
        expect(new UpdateSite('acme', 42, 99, new UpdateSiteData(repositoryBranch: 'main'))->body()->all())->toBe([
            'repository_branch' => 'main',
        ]);
    });
