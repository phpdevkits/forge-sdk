<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use PhpDevKits\ForgeSdk\Data\CreateFirewallRuleData;
use PhpDevKits\ForgeSdk\Enums\RuleType;
use Tests\Factories\CreateFirewallRuleDataFactory;

beforeEach(function (): void {
    $this->factory = new CreateFirewallRuleDataFactory;
});

test('factory produces a usable CreateFirewallRuleData',
    function (): void {
        $data = $this->factory->make();

        expect($data)->toBeInstanceOf(CreateFirewallRuleData::class)
            ->and($data->name)->toBeString()->not->toBeEmpty()
            ->and($data->type)->toBe(RuleType::Allow);
    });

test('toArray() emits only required fields when port and ip_address are null',
    function (): void {
        $data = new CreateFirewallRuleData(name: 'Allow HTTP', type: RuleType::Allow);

        expect($data->toArray())->toBe([
            'name' => 'Allow HTTP',
            'type' => 'allow',
        ]);
    });

test('toArray() emits port when set',
    function (): void {
        $data = new CreateFirewallRuleData(name: 'Allow MySQL', type: RuleType::Allow, port: '3306');

        expect($data->toArray())->toBe([
            'name' => 'Allow MySQL',
            'type' => 'allow',
            'port' => '3306',
        ]);
    });

test('toArray() emits ip_address and the deny type when set',
    function (): void {
        $data = new CreateFirewallRuleData(name: 'Block host', type: RuleType::Deny, port: '22', ipAddress: '192.168.1.1');

        expect($data->toArray())->toBe([
            'name' => 'Block host',
            'type' => 'deny',
            'port' => '22',
            'ip_address' => '192.168.1.1',
        ]);
    });
