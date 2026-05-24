<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use PhpDevKits\ForgeSdk\Data\ListSitesOptions;

test('toQuery() returns an empty array when no options are set',
    function (): void {
        expect((new ListSitesOptions)->toQuery())->toBe([]);
    });

test('toQuery() emits page[size] and page[cursor]',
    function (): void {
        expect(new ListSitesOptions(size: 15, cursor: 'CURSOR-A')->toQuery())->toBe([
            'page[size]' => 15,
            'page[cursor]' => 'CURSOR-A',
        ]);
    });

test('toQuery() emits sort when set',
    function (): void {
        expect(new ListSitesOptions(sort: '-created_at')->toQuery())->toBe(['sort' => '-created_at']);
    });

test('toQuery() emits filter[name] when set',
    function (): void {
        expect(new ListSitesOptions(name: 'example.com')->toQuery())->toBe(['filter[name]' => 'example.com']);
    });

test('toQuery() emits all set fields together',
    function (): void {
        expect(new ListSitesOptions(size: 5, cursor: 'X', sort: 'name', name: 'a')->toQuery())->toBe([
            'page[size]' => 5,
            'page[cursor]' => 'X',
            'sort' => 'name',
            'filter[name]' => 'a',
        ]);
    });
