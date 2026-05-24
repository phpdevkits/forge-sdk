<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use DateTimeImmutable;
use InvalidArgumentException;
use PhpDevKits\ForgeSdk\Data\Deployment;
use PhpDevKits\ForgeSdk\Data\DeploymentCommit;
use Tests\Factories\DeploymentFactory;

beforeEach(function (): void {
    $this->factory = new DeploymentFactory;

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    $this->attrs = static fn (array $overrides = []): array => array_merge([
        'commit' => ['hash' => 'abc123', 'author' => 'Jane Dev', 'message' => 'Ship it', 'branch' => 'main'],
        'type' => 'deployment',
        'status' => 'finished',
        'started_at' => '2026-01-01T00:00:00Z',
        'ended_at' => '2026-01-01T00:01:00Z',
        'created_at' => '2026-01-01T00:00:00Z',
        'updated_at' => '2026-01-01T00:01:00Z',
    ], $overrides);
});

test('factory produces a usable Deployment',
    function (): void {
        $deployment = $this->factory->make();

        expect($deployment)->toBeInstanceOf(Deployment::class)
            ->and($deployment->commit)->toBeInstanceOf(DeploymentCommit::class);
    });

test('::from() hydrates a full deployment',
    function (): void {
        $deployment = Deployment::from(['id' => '5', 'type' => 'deployments', 'attributes' => ($this->attrs)()]);

        expect($deployment->id)->toBe('5')
            ->and($deployment->commit)->toBeInstanceOf(DeploymentCommit::class)
            ->and($deployment->commit->hash)->toBe('abc123')
            ->and($deployment->type)->toBe('deployment')
            ->and($deployment->status)->toBe('finished')
            ->and($deployment->startedAt?->format('Y-m-d'))->toBe('2026-01-01')
            ->and($deployment->endedAt)->toBeInstanceOf(DateTimeImmutable::class);
    });

test('::from() accepts null started_at/ended_at (queued deployment)',
    function (): void {
        $deployment = Deployment::from(['id' => '1', 'attributes' => ($this->attrs)([
            'status' => 'queued',
            'started_at' => null,
            'ended_at' => null,
        ])]);

        expect($deployment->startedAt)->toBeNull()
            ->and($deployment->endedAt)->toBeNull();
    });

test('::from() throws when id is missing',
    function (): void {
        Deployment::from(['attributes' => []]);
    })->throws(InvalidArgumentException::class, 'missing the `id` field');

test('::from() throws when attributes is missing',
    function (): void {
        Deployment::from(['id' => '1']);
    })->throws(InvalidArgumentException::class, 'missing the `attributes` object');

test('::from() throws when commit object is missing',
    function (): void {
        $attrs = ($this->attrs)();
        unset($attrs['commit']);
        Deployment::from(['id' => '1', 'attributes' => $attrs]);
    })->throws(InvalidArgumentException::class, '`attributes.commit` must be an object');

test('::from() throws when status is missing',
    function (): void {
        $attrs = ($this->attrs)();
        unset($attrs['status']);
        Deployment::from(['id' => '1', 'attributes' => $attrs]);
    })->throws(InvalidArgumentException::class, '`attributes.status` must be a string');

test('::from() throws when started_at is the wrong type',
    function (): void {
        Deployment::from(['id' => '1', 'attributes' => ($this->attrs)(['started_at' => 42])]);
    })->throws(InvalidArgumentException::class, '`attributes.started_at` must be a string or null');

test('::from() throws when a required date is missing',
    function (): void {
        $attrs = ($this->attrs)();
        unset($attrs['created_at']);
        Deployment::from(['id' => '1', 'attributes' => $attrs]);
    })->throws(InvalidArgumentException::class, '`attributes.created_at` must be a string');

test('::from() throws when a required date is malformed',
    function (): void {
        Deployment::from(['id' => '1', 'attributes' => ($this->attrs)(['created_at' => 'not-a-date'])]);
    })->throws(InvalidArgumentException::class, '`attributes.created_at` is not a valid date-time');

test('::from() throws when started_at is a malformed date',
    function (): void {
        Deployment::from(['id' => '1', 'attributes' => ($this->attrs)(['started_at' => 'not-a-date'])]);
    })->throws(InvalidArgumentException::class, '`attributes.started_at` is not a valid date-time');

test('jsonSerialize() emits snake_case keys with nested commit and ATOM dates',
    function (): void {
        $deployment = $this->factory->make([
            'id' => '9',
            'startedAt' => new DateTimeImmutable('2026-01-01T00:00:00+00:00'),
            'endedAt' => null,
            'createdAt' => new DateTimeImmutable('2026-01-01T00:00:00+00:00'),
            'updatedAt' => new DateTimeImmutable('2026-01-01T00:01:00+00:00'),
        ]);

        $json = $deployment->jsonSerialize();

        expect($json['id'])->toBe('9')
            ->and($json['commit'])->toBeInstanceOf(DeploymentCommit::class)
            ->and($json['started_at'])->toBe('2026-01-01T00:00:00+00:00')
            ->and($json['ended_at'])->toBeNull();
    });
