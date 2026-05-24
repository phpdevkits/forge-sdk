<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use DateTimeImmutable;
use InvalidArgumentException;
use PhpDevKits\ForgeSdk\Data\Daemon;
use Tests\Factories\DaemonFactory;

beforeEach(function (): void {
    $this->factory = new DaemonFactory;

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    $this->attrs = static fn (array $overrides = []): array => array_merge([
        'command' => 'php artisan queue:work',
        'user' => 'forge',
        'directory' => '/home/forge/example.com',
        'processes' => 2,
        'status' => 'running',
        'created_at' => '2026-01-01T00:00:00Z',
    ], $overrides);
});

test('factory produces a usable Daemon',
    function (): void {
        expect($this->factory->make())->toBeInstanceOf(Daemon::class);
    });

test('::from() hydrates a full daemon',
    function (): void {
        $daemon = Daemon::from(['id' => '7', 'type' => 'backgroundProcesses', 'attributes' => ($this->attrs)()]);

        expect($daemon->id)->toBe('7')
            ->and($daemon->command)->toBe('php artisan queue:work')
            ->and($daemon->user)->toBe('forge')
            ->and($daemon->directory)->toBe('/home/forge/example.com')
            ->and($daemon->processes)->toBe(2)
            ->and($daemon->status)->toBe('running')
            ->and($daemon->createdAt->format('Y-m-d'))->toBe('2026-01-01');
    });

test('::from() accepts null directory',
    function (): void {
        expect(Daemon::from(['id' => '1', 'attributes' => ($this->attrs)(['directory' => null])])->directory)->toBeNull();
    });

test('::from() throws when id is missing',
    function (): void {
        Daemon::from(['attributes' => []]);
    })->throws(InvalidArgumentException::class, 'missing the `id` field');

test('::from() throws when attributes is missing',
    function (): void {
        Daemon::from(['id' => '1']);
    })->throws(InvalidArgumentException::class, 'missing the `attributes` object');

test('::from() throws when processes is not an integer',
    function (): void {
        Daemon::from(['id' => '1', 'attributes' => ($this->attrs)(['processes' => '2'])]);
    })->throws(InvalidArgumentException::class, '`attributes.processes` must be an integer');

test('::from() throws when a required string field is missing',
    function (): void {
        $attrs = ($this->attrs)();
        unset($attrs['command']);
        Daemon::from(['id' => '1', 'attributes' => $attrs]);
    })->throws(InvalidArgumentException::class, '`attributes.command` must be a string');

test('::from() throws when directory is the wrong type',
    function (): void {
        Daemon::from(['id' => '1', 'attributes' => ($this->attrs)(['directory' => 42])]);
    })->throws(InvalidArgumentException::class, '`attributes.directory` must be a string or null');

test('::from() throws when created_at is missing',
    function (): void {
        $attrs = ($this->attrs)();
        unset($attrs['created_at']);
        Daemon::from(['id' => '1', 'attributes' => $attrs]);
    })->throws(InvalidArgumentException::class, '`attributes.created_at` must be a string');

test('::from() throws when created_at is malformed',
    function (): void {
        Daemon::from(['id' => '1', 'attributes' => ($this->attrs)(['created_at' => 'not-a-date'])]);
    })->throws(InvalidArgumentException::class, '`attributes.created_at` is not a valid date-time');

test('jsonSerialize() emits snake_case with an ATOM date',
    function (): void {
        $daemon = $this->factory->make([
            'id' => '9',
            'createdAt' => new DateTimeImmutable('2026-01-01T00:00:00+00:00'),
        ]);

        $json = $daemon->jsonSerialize();

        expect($json['id'])->toBe('9')
            ->and($json)->toHaveKey('processes')
            ->and($json['created_at'])->toBe('2026-01-01T00:00:00+00:00');
    });
