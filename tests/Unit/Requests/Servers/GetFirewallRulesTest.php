<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Servers;

use PhpDevKits\ForgeSdk\Data\ListFirewallRulesOptions;
use PhpDevKits\ForgeSdk\Requests\Servers\GetFirewallRules;
use Saloon\Enums\Method;

test('resolveEndpoint() interpolates the org slug and server id',
    function (): void {
        $request = new GetFirewallRules('acme', 42);

        expect($request->resolveEndpoint())->toBe('/orgs/acme/servers/42/firewall-rules');
    });

test('uses the GET method',
    function (): void {
        expect(new GetFirewallRules('acme', 42)->getMethod())->toBe(Method::GET);
    });

test('emits options as query parameters when supplied',
    function (): void {
        $request = new GetFirewallRules('acme', 42, new ListFirewallRulesOptions(size: 5, type: 'allow'));

        expect($request->query()->all())->toBe([
            'page[size]' => 5,
            'filter[type]' => 'allow',
        ]);
    });
