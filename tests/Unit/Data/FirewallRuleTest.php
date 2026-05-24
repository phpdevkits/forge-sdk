<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use DateTimeImmutable;
use InvalidArgumentException;
use PhpDevKits\ForgeSdk\Data\FirewallRule;
use Tests\Factories\FirewallRuleFactory;

beforeEach(function (): void {
    $this->factory = new FirewallRuleFactory;
});

test('factory produces a usable FirewallRule',
    function (): void {
        $rule = $this->factory->make();

        expect($rule)->toBeInstanceOf(FirewallRule::class)
            ->and($rule->id)->toBeString()->not->toBeEmpty()
            ->and($rule->createdAt)->toBeInstanceOf(DateTimeImmutable::class);
    });

test('::from() hydrates a full JSON:API resource object',
    function (): void {
        $rule = FirewallRule::from([
            'id' => '42',
            'type' => 'rules',
            'attributes' => [
                'name' => 'Allow HTTP',
                'port' => '80',
                'type' => 'allow',
                'ip_address' => '192.168.1.1',
                'status' => 'installed',
                'created_at' => '2026-04-01T00:00:00Z',
                'updated_at' => '2026-05-01T00:00:00Z',
            ],
        ]);

        expect($rule->id)->toBe('42')
            ->and($rule->name)->toBe('Allow HTTP')
            ->and($rule->port)->toBe('80')
            ->and($rule->type)->toBe('allow')
            ->and($rule->ipAddress)->toBe('192.168.1.1')
            ->and($rule->status)->toBe('installed')
            ->and($rule->createdAt->format('Y-m-d'))->toBe('2026-04-01');
    });

test('::from() accepts null port, ip_address and status',
    function (): void {
        $rule = FirewallRule::from([
            'id' => '1',
            'attributes' => [
                'name' => 'Allow all',
                'port' => null,
                'type' => 'allow',
                'ip_address' => null,
                'status' => null,
                'created_at' => '2026-01-01T00:00:00Z',
                'updated_at' => '2026-01-01T00:00:00Z',
            ],
        ]);

        expect($rule->port)->toBeNull()
            ->and($rule->ipAddress)->toBeNull()
            ->and($rule->status)->toBeNull();
    });

test('::from() throws when id is missing',
    function (): void {
        FirewallRule::from(['attributes' => []]);
    })->throws(InvalidArgumentException::class, 'missing the `id` field');

test('::from() throws when attributes is missing',
    function (): void {
        FirewallRule::from(['id' => '1']);
    })->throws(InvalidArgumentException::class, 'missing the `attributes` object');

test('::from() throws when a required string field is missing',
    function (): void {
        FirewallRule::from(['id' => '1', 'attributes' => []]);
    })->throws(InvalidArgumentException::class, '`attributes.name` must be a string');

test('::from() throws when an optional string field is the wrong type',
    function (): void {
        FirewallRule::from(['id' => '1', 'attributes' => [
            'name' => 'Allow HTTP', 'type' => 'allow',
            'port' => 80,
            'created_at' => '2026-01-01T00:00:00Z',
            'updated_at' => '2026-01-01T00:00:00Z',
        ]]);
    })->throws(InvalidArgumentException::class, '`attributes.port` must be a string or null');

test('::from() throws when a required date is missing',
    function (): void {
        FirewallRule::from(['id' => '1', 'attributes' => [
            'name' => 'Allow HTTP', 'type' => 'allow',
        ]]);
    })->throws(InvalidArgumentException::class, '`attributes.created_at` must be a string');

test('::from() throws when a required date is malformed',
    function (): void {
        FirewallRule::from(['id' => '1', 'attributes' => [
            'name' => 'Allow HTTP', 'type' => 'allow',
            'created_at' => 'not-a-date',
            'updated_at' => '2026-01-01T00:00:00Z',
        ]]);
    })->throws(InvalidArgumentException::class, '`attributes.created_at` is not a valid date-time');

test('jsonSerialize() emits snake_case keys with ATOM-formatted dates',
    function (): void {
        $rule = $this->factory->make([
            'id' => '99',
            'port' => '443',
            'ipAddress' => null,
            'createdAt' => new DateTimeImmutable('2026-01-01T00:00:00+00:00'),
            'updatedAt' => new DateTimeImmutable('2026-02-01T00:00:00+00:00'),
        ]);

        $json = $rule->jsonSerialize();

        expect($json['id'])->toBe('99')
            ->and($json['port'])->toBe('443')
            ->and($json)->toHaveKey('ip_address')
            ->and($json['ip_address'])->toBeNull()
            ->and($json['created_at'])->toBe('2026-01-01T00:00:00+00:00')
            ->and($json['updated_at'])->toBe('2026-02-01T00:00:00+00:00');
    });
