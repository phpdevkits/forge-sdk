<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use PhpDevKits\ForgeSdk\Data\ListBackupInstancesOptions;

test('toQuery() returns an empty array when no options are set',
    function (): void {
        expect((new ListBackupInstancesOptions)->toQuery())->toBe([]);
    });

test('toQuery() emits page[size] and page[cursor]',
    function (): void {
        expect(new ListBackupInstancesOptions(size: 10, cursor: 'CURSOR-A')->toQuery())->toBe([
            'page[size]' => 10,
            'page[cursor]' => 'CURSOR-A',
        ]);
    });

test('toQuery() emits sort when set',
    function (): void {
        expect(new ListBackupInstancesOptions(sort: '-created_at')->toQuery())->toBe(['sort' => '-created_at']);
    });

test('toQuery() emits filter[status] when set',
    function (): void {
        expect(new ListBackupInstancesOptions(status: 'finished')->toQuery())->toBe([
            'filter[status]' => 'finished',
        ]);
    });

test('toQuery() emits all set fields together',
    function (): void {
        $options = new ListBackupInstancesOptions(size: 5, cursor: 'X', sort: 'created_at', status: 'pending');

        expect($options->toQuery())->toBe([
            'page[size]' => 5,
            'page[cursor]' => 'X',
            'sort' => 'created_at',
            'filter[status]' => 'pending',
        ]);
    });
