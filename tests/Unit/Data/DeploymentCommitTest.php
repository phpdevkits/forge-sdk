<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use InvalidArgumentException;
use PhpDevKits\ForgeSdk\Data\DeploymentCommit;
use Tests\Factories\DeploymentCommitFactory;

beforeEach(function (): void {
    $this->factory = new DeploymentCommitFactory;
});

test('factory produces a usable DeploymentCommit',
    function (): void {
        expect($this->factory->make())->toBeInstanceOf(DeploymentCommit::class);
    });

test('::from() hydrates all commit fields',
    function (): void {
        $commit = DeploymentCommit::from([
            'hash' => 'abc123',
            'author' => 'Jane Dev',
            'message' => 'Ship it',
            'branch' => 'main',
        ]);

        expect($commit->hash)->toBe('abc123')
            ->and($commit->author)->toBe('Jane Dev')
            ->and($commit->message)->toBe('Ship it')
            ->and($commit->branch)->toBe('main');
    });

test('::from() accepts all-null commit fields',
    function (): void {
        $commit = DeploymentCommit::from(['hash' => null, 'author' => null, 'message' => null, 'branch' => null]);

        expect($commit->hash)->toBeNull()
            ->and($commit->branch)->toBeNull();
    });

test('::from() throws when a field is the wrong type',
    function (): void {
        DeploymentCommit::from(['hash' => 42]);
    })->throws(InvalidArgumentException::class, '`hash` must be a string or null');

test('jsonSerialize() round-trips all fields',
    function (): void {
        expect(new DeploymentCommit(hash: 'a', author: 'b', message: 'c', branch: 'd')->jsonSerialize())->toBe([
            'hash' => 'a',
            'author' => 'b',
            'message' => 'c',
            'branch' => 'd',
        ]);
    });
