<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use DateTimeImmutable;
use InvalidArgumentException;
use PhpDevKits\ForgeSdk\Data\RedirectRule;
use Tests\Factories\RedirectRuleFactory;

beforeEach(function (): void {
    $this->factory = new RedirectRuleFactory;
});

test('factory produces a usable RedirectRule',
    function (): void {
        $rule = $this->factory->make();

        expect($rule)->toBeInstanceOf(RedirectRule::class)
            ->and($rule->id)->toBeString()->not->toBeEmpty()
            ->and($rule->createdAt)->toBeInstanceOf(DateTimeImmutable::class);
    });

test('::from() hydrates a full JSON:API resource object',
    function (): void {
        $rule = RedirectRule::from([
            'id' => '42',
            'type' => 'redirect-rules',
            'attributes' => [
                'from' => '/old-path',
                'to' => '/new-path',
                'type' => 'permanent',
                'status' => 'installed',
                'created_at' => '2026-04-01T00:00:00Z',
                'updated_at' => '2026-05-01T00:00:00Z',
            ],
        ]);

        expect($rule->id)->toBe('42')
            ->and($rule->from)->toBe('/old-path')
            ->and($rule->to)->toBe('/new-path')
            ->and($rule->type)->toBe('permanent')
            ->and($rule->status)->toBe('installed')
            ->and($rule->createdAt->format('Y-m-d'))->toBe('2026-04-01');
    });

test('::from() throws when id is missing',
    function (): void {
        RedirectRule::from(['attributes' => []]);
    })->throws(InvalidArgumentException::class, 'missing the `id` field');

test('::from() throws when attributes is missing',
    function (): void {
        RedirectRule::from(['id' => '1']);
    })->throws(InvalidArgumentException::class, 'missing the `attributes` object');

test('::from() throws when a required string field is missing',
    function (): void {
        RedirectRule::from(['id' => '1', 'attributes' => []]);
    })->throws(InvalidArgumentException::class, '`attributes.from` must be a string');

test('::from() throws when a required date is missing',
    function (): void {
        RedirectRule::from(['id' => '1', 'attributes' => [
            'from' => '/a', 'to' => '/b', 'type' => 'redirect', 'status' => 'installed',
        ]]);
    })->throws(InvalidArgumentException::class, '`attributes.created_at` must be a string');

test('::from() throws when a required date is malformed',
    function (): void {
        RedirectRule::from(['id' => '1', 'attributes' => [
            'from' => '/a', 'to' => '/b', 'type' => 'redirect', 'status' => 'installed',
            'created_at' => 'not-a-date',
            'updated_at' => '2026-01-01T00:00:00Z',
        ]]);
    })->throws(InvalidArgumentException::class, '`attributes.created_at` is not a valid date-time');

test('jsonSerialize() emits snake_case keys with ATOM-formatted dates',
    function (): void {
        $rule = $this->factory->make([
            'id' => '99',
            'from' => '/old-path',
            'to' => '/new-path',
            'type' => 'permanent',
            'status' => 'installed',
            'createdAt' => new DateTimeImmutable('2026-01-01T00:00:00+00:00'),
            'updatedAt' => new DateTimeImmutable('2026-02-01T00:00:00+00:00'),
        ]);

        $json = $rule->jsonSerialize();

        expect($json)->toBe([
            'id' => '99',
            'from' => '/old-path',
            'to' => '/new-path',
            'type' => 'permanent',
            'status' => 'installed',
            'created_at' => '2026-01-01T00:00:00+00:00',
            'updated_at' => '2026-02-01T00:00:00+00:00',
        ]);
    });
