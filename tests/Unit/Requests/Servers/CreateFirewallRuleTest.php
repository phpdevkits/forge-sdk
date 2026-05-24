<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Servers;

use PhpDevKits\ForgeSdk\Data\CreateFirewallRuleData;
use PhpDevKits\ForgeSdk\Enums\RuleType;
use PhpDevKits\ForgeSdk\Requests\Servers\CreateFirewallRule;
use Saloon\Enums\Method;

test('resolveEndpoint() interpolates the org slug and server id',
    function (): void {
        $data = new CreateFirewallRuleData(name: 'Allow HTTP', type: RuleType::Allow);

        expect(new CreateFirewallRule('acme', 42, $data)->resolveEndpoint())->toBe('/orgs/acme/servers/42/firewall-rules');
    });

test('uses the POST method',
    function (): void {
        $data = new CreateFirewallRuleData(name: 'Allow HTTP', type: RuleType::Allow);

        expect(new CreateFirewallRule('acme', 42, $data)->getMethod())->toBe(Method::POST);
    });

test('body() reflects the CreateFirewallRuleData payload',
    function (): void {
        $data = new CreateFirewallRuleData(name: 'Allow MySQL', type: RuleType::Allow, port: '3306', ipAddress: '192.168.1.1');

        expect(new CreateFirewallRule('acme', 42, $data)->body()->all())->toBe([
            'name' => 'Allow MySQL',
            'type' => 'allow',
            'port' => '3306',
            'ip_address' => '192.168.1.1',
        ]);
    });
