<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Servers;

use PhpDevKits\ForgeSdk\Requests\Servers\GetFirewallRule;
use Saloon\Enums\Method;

test('resolveEndpoint() interpolates the org slug, server id and rule id',
    function (): void {
        $request = new GetFirewallRule('acme', 42, 7);

        expect($request->resolveEndpoint())->toBe('/orgs/acme/servers/42/firewall-rules/7');
    });

test('uses the GET method',
    function (): void {
        expect(new GetFirewallRule('acme', 42, 7)->getMethod())->toBe(Method::GET);
    });
