<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use PhpDevKits\ForgeSdk\Data\ListDatabasesOptions;

test('toQuery() returns an empty array when no options are set',
    function (): void {
        expect((new ListDatabasesOptions)->toQuery())->toBe([]);
    });

test('toQuery() emits page[size] and page[cursor]',
    function (): void {
        expect(new ListDatabasesOptions(size: 10, cursor: 'CURSOR-A')->toQuery())->toBe([
            'page[size]' => 10,
            'page[cursor]' => 'CURSOR-A',
        ]);
    });

test('toQuery() emits sort when set',
    function (): void {
        expect(new ListDatabasesOptions(sort: '-name')->toQuery())->toBe(['sort' => '-name']);
    });

test('toQuery() emits filter[name] when set',
    function (): void {
        expect(new ListDatabasesOptions(name: 'forge')->toQuery())->toBe(['filter[name]' => 'forge']);
    });

test('toQuery() emits filter[status] when set',
    function (): void {
        expect(new ListDatabasesOptions(status: 'installed')->toQuery())->toBe(['filter[status]' => 'installed']);
    });

test('toQuery() emits all set fields together',
    function (): void {
        expect(new ListDatabasesOptions(size: 5, cursor: 'X', sort: 'name', name: 'forge', status: 'installed')->toQuery())->toBe([
            'page[size]' => 5,
            'page[cursor]' => 'X',
            'sort' => 'name',
            'filter[name]' => 'forge',
            'filter[status]' => 'installed',
        ]);
    });
