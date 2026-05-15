<?php

declare(strict_types=1);

namespace Tests\Unit\Enums;

use PhpDevKits\ForgeSdk\Enums\ServerType;

test('ServerType exposes the canonical Forge server-role slugs',
    function (): void {
        expect(ServerType::App->value)->toBe('app')
            ->and(ServerType::Web->value)->toBe('web')
            ->and(ServerType::LoadBalancer->value)->toBe('loadbalancer')
            ->and(ServerType::Database->value)->toBe('database')
            ->and(ServerType::Cache->value)->toBe('cache')
            ->and(ServerType::Worker->value)->toBe('worker')
            ->and(ServerType::Meilisearch->value)->toBe('meilisearch')
            ->and(ServerType::OpenClaw->value)->toBe('openclaw');
    });

test('ServerType::tryFrom returns null for unknown roles',
    function (): void {
        expect(ServerType::tryFrom('kubernetes'))->toBeNull();
    });
