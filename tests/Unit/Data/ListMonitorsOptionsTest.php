<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use PhpDevKits\ForgeSdk\Data\ListMonitorsOptions;

test('toQuery() returns an empty array when no options are set',
    function (): void {
        expect((new ListMonitorsOptions)->toQuery())->toBe([]);
    });

test('toQuery() emits page[size] and page[cursor]',
    function (): void {
        expect(new ListMonitorsOptions(size: 10, cursor: 'CURSOR-A')->toQuery())->toBe([
            'page[size]' => 10,
            'page[cursor]' => 'CURSOR-A',
        ]);
    });

test('toQuery() emits filter[status] when set',
    function (): void {
        expect(new ListMonitorsOptions(status: 'installed')->toQuery())->toBe(['filter[status]' => 'installed']);
    });

test('toQuery() emits filter[state] when set',
    function (): void {
        expect(new ListMonitorsOptions(state: 'OK')->toQuery())->toBe(['filter[state]' => 'OK']);
    });

test('toQuery() emits filter[type] when set',
    function (): void {
        expect(new ListMonitorsOptions(type: 'cpu_load')->toQuery())->toBe(['filter[type]' => 'cpu_load']);
    });

test('toQuery() emits filter[notify] when set',
    function (): void {
        expect(new ListMonitorsOptions(notify: 'taylor@laravel.com')->toQuery())->toBe(['filter[notify]' => 'taylor@laravel.com']);
    });

test('toQuery() emits sort when set',
    function (): void {
        expect(new ListMonitorsOptions(sort: '-state')->toQuery())->toBe(['sort' => '-state']);
    });

test('toQuery() emits all set fields together',
    function (): void {
        expect(new ListMonitorsOptions(
            size: 5,
            cursor: 'X',
            status: 'installed',
            state: 'OK',
            type: 'cpu_load',
            notify: 'taylor@laravel.com',
            sort: 'created_at',
        )->toQuery())->toBe([
            'page[size]' => 5,
            'page[cursor]' => 'X',
            'filter[status]' => 'installed',
            'filter[state]' => 'OK',
            'filter[type]' => 'cpu_load',
            'filter[notify]' => 'taylor@laravel.com',
            'sort' => 'created_at',
        ]);
    });
