<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Servers;

use PhpDevKits\ForgeSdk\Requests\Servers\DeleteFirewallRule;
use Saloon\Enums\Method;

test('resolveEndpoint() interpolates the org slug, server id and rule id',
    function (): void {
        expect(new DeleteFirewallRule('acme', 42, 7)->resolveEndpoint())->toBe('/orgs/acme/servers/42/firewall-rules/7');
    });

test('uses the DELETE method',
    function (): void {
        expect(new DeleteFirewallRule('acme', 42, 7)->getMethod())->toBe(Method::DELETE);
    });
