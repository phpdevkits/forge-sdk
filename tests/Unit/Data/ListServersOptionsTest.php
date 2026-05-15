<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use PhpDevKits\ForgeSdk\Data\ListServersOptions;

test('toQuery() returns an empty array when no options are set',
    function (): void {
        expect((new ListServersOptions)->toQuery())->toBe([]);
    });

test('toQuery() emits page[size] and page[cursor]',
    function (): void {
        expect(new ListServersOptions(size: 25, cursor: 'CURSOR-A')->toQuery())->toBe([
            'page[size]' => 25,
            'page[cursor]' => 'CURSOR-A',
        ]);
    });

test('toQuery() emits sort when set',
    function (): void {
        expect(new ListServersOptions(sort: '-created_at')->toQuery())->toBe([
            'sort' => '-created_at',
        ]);
    });

test('toQuery() emits filter[ip_address] when set',
    function (): void {
        expect(new ListServersOptions(ipAddress: '192.168.1.1')->toQuery())->toBe([
            'filter[ip_address]' => '192.168.1.1',
        ]);
    });

test('toQuery() emits filter[name] when set',
    function (): void {
        expect(new ListServersOptions(name: 'production-web-01')->toQuery())->toBe([
            'filter[name]' => 'production-web-01',
        ]);
    });

test('toQuery() emits filter[region] when set',
    function (): void {
        expect(new ListServersOptions(region: 'nyc3')->toQuery())->toBe([
            'filter[region]' => 'nyc3',
        ]);
    });

test('toQuery() emits filter[size] when sizeFilter is set',
    function (): void {
        expect(new ListServersOptions(sizeFilter: 's-2vcpu-2gb')->toQuery())->toBe([
            'filter[size]' => 's-2vcpu-2gb',
        ]);
    });

test('toQuery() emits filter[provider] when set',
    function (): void {
        expect(new ListServersOptions(provider: 'aws')->toQuery())->toBe([
            'filter[provider]' => 'aws',
        ]);
    });

test('toQuery() emits filter[ubuntu_version] when set',
    function (): void {
        expect(new ListServersOptions(ubuntuVersion: '24.04')->toQuery())->toBe([
            'filter[ubuntu_version]' => '24.04',
        ]);
    });

test('toQuery() emits filter[php_version] when set',
    function (): void {
        expect(new ListServersOptions(phpVersion: 'php83')->toQuery())->toBe([
            'filter[php_version]' => 'php83',
        ]);
    });

test('toQuery() emits filter[database_type] when set',
    function (): void {
        expect(new ListServersOptions(databaseType: 'mysql')->toQuery())->toBe([
            'filter[database_type]' => 'mysql',
        ]);
    });

test('toQuery() emits all set fields together',
    function (): void {
        $options = new ListServersOptions(
            size: 10,
            cursor: 'CURSOR-Z',
            sort: '-created_at',
            ipAddress: '10.0.0.1',
            name: 'web',
            region: 'fra1',
            sizeFilter: 's-2vcpu-2gb',
            provider: 'hetzner',
            ubuntuVersion: '24.04',
            phpVersion: 'php84',
            databaseType: 'postgres',
        );

        expect($options->toQuery())->toBe([
            'page[size]' => 10,
            'page[cursor]' => 'CURSOR-Z',
            'sort' => '-created_at',
            'filter[ip_address]' => '10.0.0.1',
            'filter[name]' => 'web',
            'filter[region]' => 'fra1',
            'filter[size]' => 's-2vcpu-2gb',
            'filter[provider]' => 'hetzner',
            'filter[ubuntu_version]' => '24.04',
            'filter[php_version]' => 'php84',
            'filter[database_type]' => 'postgres',
        ]);
    });
