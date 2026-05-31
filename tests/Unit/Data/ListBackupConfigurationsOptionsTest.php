<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use PhpDevKits\ForgeSdk\Data\ListBackupConfigurationsOptions;

test('toQuery() returns an empty array when no options are set',
    function (): void {
        expect((new ListBackupConfigurationsOptions)->toQuery())->toBe([]);
    });

test('toQuery() emits page[size] and page[cursor]',
    function (): void {
        expect(new ListBackupConfigurationsOptions(size: 10, cursor: 'CURSOR-A')->toQuery())->toBe([
            'page[size]' => 10,
            'page[cursor]' => 'CURSOR-A',
        ]);
    });

test('toQuery() emits sort when set',
    function (): void {
        expect(new ListBackupConfigurationsOptions(sort: '-name')->toQuery())->toBe(['sort' => '-name']);
    });

test('toQuery() emits filter[name] when set',
    function (): void {
        expect(new ListBackupConfigurationsOptions(name: 'daily-backup')->toQuery())->toBe(['filter[name]' => 'daily-backup']);
    });

test('toQuery() emits filter[status] when set',
    function (): void {
        expect(new ListBackupConfigurationsOptions(status: 'installed')->toQuery())->toBe(['filter[status]' => 'installed']);
    });

test('toQuery() emits all set fields together',
    function (): void {
        expect(new ListBackupConfigurationsOptions(size: 5, cursor: 'X', sort: 'name', name: 'daily', status: 'installed')->toQuery())->toBe([
            'page[size]' => 5,
            'page[cursor]' => 'X',
            'sort' => 'name',
            'filter[name]' => 'daily',
            'filter[status]' => 'installed',
        ]);
    });
