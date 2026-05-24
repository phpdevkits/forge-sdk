<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use InvalidArgumentException;
use PhpDevKits\ForgeSdk\Data\SiteRepository;
use Tests\Factories\SiteRepositoryFactory;

beforeEach(function (): void {
    $this->factory = new SiteRepositoryFactory;
});

test('factory produces a usable SiteRepository',
    function (): void {
        expect($this->factory->make())->toBeInstanceOf(SiteRepository::class);
    });

test('::from() hydrates a connected repository',
    function (): void {
        $repo = SiteRepository::from([
            'provider' => 'github',
            'url' => 'acme/site',
            'branch' => 'main',
            'status' => 'installed',
        ]);

        expect($repo->provider)->toBe('github')
            ->and($repo->url)->toBe('acme/site')
            ->and($repo->branch)->toBe('main')
            ->and($repo->status)->toBe('installed');
    });

test('::from() accepts a bare provider with null url/branch/status',
    function (): void {
        $repo = SiteRepository::from(['provider' => 'github', 'url' => null, 'branch' => null, 'status' => null]);

        expect($repo->url)->toBeNull()
            ->and($repo->branch)->toBeNull()
            ->and($repo->status)->toBeNull();
    });

test('::from() throws when provider is missing',
    function (): void {
        SiteRepository::from(['url' => 'x']);
    })->throws(InvalidArgumentException::class, '`provider` must be a string');

test('::from() throws when an optional field is the wrong type',
    function (): void {
        SiteRepository::from(['provider' => 'github', 'branch' => 42]);
    })->throws(InvalidArgumentException::class, '`branch` must be a string or null');

test('jsonSerialize() round-trips all fields',
    function (): void {
        $repo = new SiteRepository(provider: 'gitlab', url: 'a/b', branch: 'dev', status: 'installing');

        expect($repo->jsonSerialize())->toBe([
            'provider' => 'gitlab',
            'url' => 'a/b',
            'branch' => 'dev',
            'status' => 'installing',
        ]);
    });
