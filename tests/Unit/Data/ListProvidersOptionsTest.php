<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use PhpDevKits\ForgeSdk\Data\ListProvidersOptions;

test('toQuery() returns an empty array when no options are set',
    function (): void {
        expect((new ListProvidersOptions)->toQuery())->toBe([]);
    });

test('toQuery() emits page[size] when size is set',
    function (): void {
        expect(new ListProvidersOptions(size: 50)->toQuery())->toBe(['page[size]' => 50]);
    });

test('toQuery() emits page[cursor] when cursor is set',
    function (): void {
        expect(new ListProvidersOptions(cursor: 'abc')->toQuery())->toBe(['page[cursor]' => 'abc']);
    });

test('toQuery() emits both when set together',
    function (): void {
        expect(new ListProvidersOptions(size: 10, cursor: 'xyz')->toQuery())->toBe([
            'page[size]' => 10,
            'page[cursor]' => 'xyz',
        ]);
    });
