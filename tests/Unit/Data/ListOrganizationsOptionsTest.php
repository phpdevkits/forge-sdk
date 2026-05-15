<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use PhpDevKits\ForgeSdk\Data\ListOrganizationsOptions;

test('toQuery() returns an empty array when no options are set',
    function (): void {
        $options = new ListOrganizationsOptions;

        expect($options->toQuery())->toBe([]);
    });

test('toQuery() emits page[size] when size is set',
    function (): void {
        $options = new ListOrganizationsOptions(size: 50);

        expect($options->toQuery())->toBe(['page[size]' => 50]);
    });

test('toQuery() emits page[cursor] when cursor is set',
    function (): void {
        $options = new ListOrganizationsOptions(cursor: 'abc123');

        expect($options->toQuery())->toBe(['page[cursor]' => 'abc123']);
    });

test('toQuery() emits both size and cursor when set together',
    function (): void {
        $options = new ListOrganizationsOptions(size: 10, cursor: 'xyz');

        expect($options->toQuery())->toBe([
            'page[size]' => 10,
            'page[cursor]' => 'xyz',
        ]);
    });
