<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use PhpDevKits\ForgeSdk\Data\ListFirewallRulesOptions;

test('toQuery() returns an empty array when no options are set',
    function (): void {
        expect((new ListFirewallRulesOptions)->toQuery())->toBe([]);
    });

test('toQuery() emits page[size] and page[cursor]',
    function (): void {
        expect(new ListFirewallRulesOptions(size: 10, cursor: 'CURSOR-A')->toQuery())->toBe([
            'page[size]' => 10,
            'page[cursor]' => 'CURSOR-A',
        ]);
    });

test('toQuery() emits sort when set',
    function (): void {
        expect(new ListFirewallRulesOptions(sort: '-created_at')->toQuery())->toBe(['sort' => '-created_at']);
    });

test('toQuery() emits filter[name] when set',
    function (): void {
        expect(new ListFirewallRulesOptions(name: 'Allow HTTP')->toQuery())->toBe(['filter[name]' => 'Allow HTTP']);
    });

test('toQuery() emits filter[status] when set',
    function (): void {
        expect(new ListFirewallRulesOptions(status: 'installed')->toQuery())->toBe(['filter[status]' => 'installed']);
    });

test('toQuery() emits filter[ip_address] when set',
    function (): void {
        expect(new ListFirewallRulesOptions(ipAddress: '192.168.1.1')->toQuery())->toBe(['filter[ip_address]' => '192.168.1.1']);
    });

test('toQuery() emits filter[type] when set',
    function (): void {
        expect(new ListFirewallRulesOptions(type: 'allow')->toQuery())->toBe(['filter[type]' => 'allow']);
    });

test('toQuery() emits filter[port] when set',
    function (): void {
        expect(new ListFirewallRulesOptions(port: '3306')->toQuery())->toBe(['filter[port]' => '3306']);
    });

test('toQuery() emits all set fields together',
    function (): void {
        expect(new ListFirewallRulesOptions(
            size: 5,
            cursor: 'X',
            sort: '-created_at',
            name: 'Allow HTTP',
            status: 'installed',
            ipAddress: '192.168.1.1',
            type: 'allow',
            port: '80',
        )->toQuery())->toBe([
            'page[size]' => 5,
            'page[cursor]' => 'X',
            'sort' => '-created_at',
            'filter[name]' => 'Allow HTTP',
            'filter[status]' => 'installed',
            'filter[ip_address]' => '192.168.1.1',
            'filter[type]' => 'allow',
            'filter[port]' => '80',
        ]);
    });
