<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use PhpDevKits\ForgeSdk\Data\ListDeploymentsOptions;

test('toQuery() returns an empty array when no options are set',
    function (): void {
        expect((new ListDeploymentsOptions)->toQuery())->toBe([]);
    });

test('toQuery() emits page[size] and page[cursor]',
    function (): void {
        expect(new ListDeploymentsOptions(size: 10, cursor: 'CURSOR-A')->toQuery())->toBe([
            'page[size]' => 10,
            'page[cursor]' => 'CURSOR-A',
        ]);
    });

test('toQuery() emits sort when set',
    function (): void {
        expect(new ListDeploymentsOptions(sort: '-created_at')->toQuery())->toBe(['sort' => '-created_at']);
    });

test('toQuery() emits commit filters when set',
    function (): void {
        $options = new ListDeploymentsOptions(
            commitHash: 'abc123',
            commitMessage: 'fix',
            commitAuthor: 'jane',
        );

        expect($options->toQuery())->toBe([
            'filter[commit_hash]' => 'abc123',
            'filter[commit_message]' => 'fix',
            'filter[commit_author]' => 'jane',
        ]);
    });

test('toQuery() emits all set fields together',
    function (): void {
        $options = new ListDeploymentsOptions(size: 5, cursor: 'X', sort: 'created_at', commitHash: 'a', commitMessage: 'b', commitAuthor: 'c');

        expect($options->toQuery())->toBe([
            'page[size]' => 5,
            'page[cursor]' => 'X',
            'sort' => 'created_at',
            'filter[commit_hash]' => 'a',
            'filter[commit_message]' => 'b',
            'filter[commit_author]' => 'c',
        ]);
    });
