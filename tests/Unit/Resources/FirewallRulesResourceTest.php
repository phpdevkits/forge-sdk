<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use PhpDevKits\ForgeSdk\Data\FirewallRule;
use PhpDevKits\ForgeSdk\Data\Page;
use PhpDevKits\ForgeSdk\Forge;
use PhpDevKits\ForgeSdk\Requests\Servers\GetFirewallRules;
use Saloon\Http\Faking\MockClient;
use Tests\Utils\FirewallRuleFixture;

test('all() returns a Page<FirewallRule>',
    /**
     * @throws Throwable
     */
    function (): void {
        $token = ($_ENV['FORGE_TEST_TOKEN'] ?? '') ?: 'test-token';
        $org = ($_ENV['FORGE_TEST_ORG_SLUG'] ?? '') ?: 'test-org';
        $serverId = ($_ENV['FORGE_TEST_SERVER_ID'] ?? '') ?: '1';
        $mockClient = new MockClient([
            GetFirewallRules::class => new FirewallRuleFixture('firewall-rules/list'),
        ]);
        $forge = new Forge($token, $org)->withMockClient($mockClient);

        $page = $forge->server($serverId)->firewallRules()->all();

        expect($page)->toBeInstanceOf(Page::class)
            ->and($page->data)->not->toBeEmpty()
            ->and($page->data[0])->toBeInstanceOf(FirewallRule::class);
    });

test('iterate() yields each firewall rule',
    /**
     * @throws Throwable
     */
    function (): void {
        $token = ($_ENV['FORGE_TEST_TOKEN'] ?? '') ?: 'test-token';
        $org = ($_ENV['FORGE_TEST_ORG_SLUG'] ?? '') ?: 'test-org';
        $serverId = ($_ENV['FORGE_TEST_SERVER_ID'] ?? '') ?: '1';
        $mockClient = new MockClient([
            GetFirewallRules::class => new FirewallRuleFixture('firewall-rules/list'),
        ]);
        $forge = new Forge($token, $org)->withMockClient($mockClient);

        $rules = iterator_to_array($forge->server($serverId)->firewallRules()->iterate());

        expect($rules)->not->toBeEmpty()
            ->and($rules[0])->toBeInstanceOf(FirewallRule::class);
    });
