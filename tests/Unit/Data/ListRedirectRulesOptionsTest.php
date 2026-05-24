<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use PhpDevKits\ForgeSdk\Data\ListRedirectRulesOptions;

test('toQuery() returns an empty array when no options are set',
    function (): void {
        expect((new ListRedirectRulesOptions)->toQuery())->toBe([]);
    });

test('toQuery() emits page[size] and page[cursor]',
    function (): void {
        expect(new ListRedirectRulesOptions(size: 10, cursor: 'CURSOR-A')->toQuery())->toBe([
            'page[size]' => 10,
            'page[cursor]' => 'CURSOR-A',
        ]);
    });

test('toQuery() emits sort when set',
    function (): void {
        expect(new ListRedirectRulesOptions(sort: '-created_at')->toQuery())->toBe(['sort' => '-created_at']);
    });

test('toQuery() emits filter[from] when set',
    function (): void {
        expect(new ListRedirectRulesOptions(from: '/old-path')->toQuery())->toBe(['filter[from]' => '/old-path']);
    });

test('toQuery() emits filter[to] when set',
    function (): void {
        expect(new ListRedirectRulesOptions(to: '/new-path')->toQuery())->toBe(['filter[to]' => '/new-path']);
    });

test('toQuery() emits filter[type] when set',
    function (): void {
        expect(new ListRedirectRulesOptions(type: 'permanent')->toQuery())->toBe(['filter[type]' => 'permanent']);
    });

test('toQuery() emits filter[status] when set',
    function (): void {
        expect(new ListRedirectRulesOptions(status: 'installed')->toQuery())->toBe(['filter[status]' => 'installed']);
    });

test('toQuery() emits all set fields together',
    function (): void {
        expect(new ListRedirectRulesOptions(size: 5, cursor: 'X', sort: 'created_at', from: '/a', to: '/b', type: 'redirect', status: 'installed')->toQuery())->toBe([
            'page[size]' => 5,
            'page[cursor]' => 'X',
            'sort' => 'created_at',
            'filter[from]' => '/a',
            'filter[to]' => '/b',
            'filter[type]' => 'redirect',
            'filter[status]' => 'installed',
        ]);
    });
