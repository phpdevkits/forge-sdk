<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use PhpDevKits\ForgeSdk\Data\ListServerCredentialsOptions;

test('toQuery() returns an empty array when no options are set',
    function (): void {
        expect((new ListServerCredentialsOptions)->toQuery())->toBe([]);
    });

test('toQuery() emits page[size] when set',
    function (): void {
        expect(new ListServerCredentialsOptions(size: 25)->toQuery())->toBe([
            'page[size]' => 25,
        ]);
    });

test('toQuery() emits page[cursor] when set',
    function (): void {
        expect(new ListServerCredentialsOptions(cursor: 'CURSOR-A')->toQuery())->toBe([
            'page[cursor]' => 'CURSOR-A',
        ]);
    });

test('toQuery() emits page[size] and page[cursor] together',
    function (): void {
        expect(new ListServerCredentialsOptions(size: 10, cursor: 'CURSOR-Z')->toQuery())->toBe([
            'page[size]' => 10,
            'page[cursor]' => 'CURSOR-Z',
        ]);
    });
