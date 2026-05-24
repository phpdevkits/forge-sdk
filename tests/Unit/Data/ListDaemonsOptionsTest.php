<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use PhpDevKits\ForgeSdk\Data\ListDaemonsOptions;

test('toQuery() returns an empty array when no options are set',
    function (): void {
        expect((new ListDaemonsOptions)->toQuery())->toBe([]);
    });

test('toQuery() emits page[size] and page[cursor]',
    function (): void {
        expect(new ListDaemonsOptions(size: 10, cursor: 'CURSOR-A')->toQuery())->toBe([
            'page[size]' => 10,
            'page[cursor]' => 'CURSOR-A',
        ]);
    });

test('toQuery() emits sort when set',
    function (): void {
        expect(new ListDaemonsOptions(sort: '-created_at')->toQuery())->toBe(['sort' => '-created_at']);
    });

test('toQuery() emits filters when set',
    function (): void {
        $options = new ListDaemonsOptions(user: 'forge', siteId: 12345, directory: '/home/forge/x');

        expect($options->toQuery())->toBe([
            'filter[user]' => 'forge',
            'filter[site_id]' => 12345,
            'filter[directory]' => '/home/forge/x',
        ]);
    });

test('toQuery() emits all set fields together',
    function (): void {
        $options = new ListDaemonsOptions(size: 5, cursor: 'X', sort: 'created_at', user: 'root', siteId: 1, directory: '/srv');

        expect($options->toQuery())->toBe([
            'page[size]' => 5,
            'page[cursor]' => 'X',
            'sort' => 'created_at',
            'filter[user]' => 'root',
            'filter[site_id]' => 1,
            'filter[directory]' => '/srv',
        ]);
    });
