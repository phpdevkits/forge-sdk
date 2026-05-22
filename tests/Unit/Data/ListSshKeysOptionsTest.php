<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use PhpDevKits\ForgeSdk\Data\ListSshKeysOptions;

test('toQuery() returns an empty array when no options are set',
    function (): void {
        expect((new ListSshKeysOptions)->toQuery())->toBe([]);
    });

test('toQuery() emits page[size] and page[cursor]',
    function (): void {
        expect(new ListSshKeysOptions(size: 10, cursor: 'CURSOR-A')->toQuery())->toBe([
            'page[size]' => 10,
            'page[cursor]' => 'CURSOR-A',
        ]);
    });

test('toQuery() emits filter[name] when set',
    function (): void {
        expect(new ListSshKeysOptions(name: 'oliver')->toQuery())->toBe(['filter[name]' => 'oliver']);
    });

test('toQuery() emits filter[user] when set',
    function (): void {
        expect(new ListSshKeysOptions(user: 'forge')->toQuery())->toBe(['filter[user]' => 'forge']);
    });

test('toQuery() emits all set fields together',
    function (): void {
        expect(new ListSshKeysOptions(size: 5, cursor: 'X', name: 'oliver', user: 'forge')->toQuery())->toBe([
            'page[size]' => 5,
            'page[cursor]' => 'X',
            'filter[name]' => 'oliver',
            'filter[user]' => 'forge',
        ]);
    });
