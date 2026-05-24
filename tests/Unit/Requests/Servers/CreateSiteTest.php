<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Servers;

use PhpDevKits\ForgeSdk\Data\CreateSiteData;
use PhpDevKits\ForgeSdk\Enums\SiteType;
use PhpDevKits\ForgeSdk\Requests\Servers\CreateSite;
use Saloon\Enums\Method;

test('resolveEndpoint() interpolates org slug and server id',
    function (): void {
        $data = new CreateSiteData(type: SiteType::Laravel);

        expect(new CreateSite('acme', 42, $data)->resolveEndpoint())->toBe('/orgs/acme/servers/42/sites');
    });

test('uses the POST method',
    function (): void {
        expect(new CreateSite('acme', 42, new CreateSiteData(type: SiteType::Php))->getMethod())->toBe(Method::POST);
    });

test('body() reflects the CreateSiteData payload',
    function (): void {
        $data = new CreateSiteData(type: SiteType::Laravel, name: 'example.com');

        expect(new CreateSite('acme', 42, $data)->body()->all())->toBe([
            'type' => 'laravel',
            'name' => 'example.com',
        ]);
    });
