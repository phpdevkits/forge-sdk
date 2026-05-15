<?php

declare(strict_types=1);

namespace Tests\Unit\Enums;

use PhpDevKits\ForgeSdk\Enums\DatabaseType;

test('DatabaseType exposes representative engine slugs',
    function (): void {
        expect(DatabaseType::Mysql->value)->toBe('mysql')
            ->and(DatabaseType::Mariadb114->value)->toBe('mariadb114')
            ->and(DatabaseType::Postgres17->value)->toBe('postgres17');
    });

test('DatabaseType::tryFrom returns null for unknown engines',
    function (): void {
        expect(DatabaseType::tryFrom('redis'))->toBeNull();
    });
