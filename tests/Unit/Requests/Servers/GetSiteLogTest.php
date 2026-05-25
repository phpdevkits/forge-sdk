<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Servers;

use PhpDevKits\ForgeSdk\Enums\SiteLog;
use PhpDevKits\ForgeSdk\Requests\Servers\GetSiteLog;
use Saloon\Enums\Method;

test('resolveEndpoint() points at the nginx access log sub-resource',
    function (): void {
        expect(new GetSiteLog('acme', 42, 7, SiteLog::NginxAccess)->resolveEndpoint())->toBe('/orgs/acme/servers/42/sites/7/logs/nginx-access');
    });

test('resolveEndpoint() points at the nginx error log sub-resource',
    function (): void {
        expect(new GetSiteLog('acme', 42, 7, SiteLog::NginxError)->resolveEndpoint())->toBe('/orgs/acme/servers/42/sites/7/logs/nginx-error');
    });

test('uses the GET method',
    function (): void {
        expect(new GetSiteLog('acme', 42, 7, SiteLog::NginxAccess)->getMethod())->toBe(Method::GET);
    });
