<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use PhpDevKits\ForgeSdk\Data\ListScheduledJobsOptions;

test('toQuery() returns an empty array when no options are set',
    function (): void {
        expect((new ListScheduledJobsOptions)->toQuery())->toBe([]);
    });

test('toQuery() emits page[size] and page[cursor]',
    function (): void {
        expect(new ListScheduledJobsOptions(size: 10, cursor: 'CURSOR-A')->toQuery())->toBe([
            'page[size]' => 10,
            'page[cursor]' => 'CURSOR-A',
        ]);
    });

test('toQuery() emits sort when set',
    function (): void {
        expect(new ListScheduledJobsOptions(sort: '-created_at')->toQuery())->toBe(['sort' => '-created_at']);
    });

test('toQuery() emits filters when set',
    function (): void {
        $options = new ListScheduledJobsOptions(status: 'installed', user: 'forge');

        expect($options->toQuery())->toBe([
            'filter[status]' => 'installed',
            'filter[user]' => 'forge',
        ]);
    });

test('toQuery() emits all set fields together',
    function (): void {
        $options = new ListScheduledJobsOptions(size: 5, cursor: 'X', sort: 'created_at', status: 'installing', user: 'root');

        expect($options->toQuery())->toBe([
            'page[size]' => 5,
            'page[cursor]' => 'X',
            'sort' => 'created_at',
            'filter[status]' => 'installing',
            'filter[user]' => 'root',
        ]);
    });
