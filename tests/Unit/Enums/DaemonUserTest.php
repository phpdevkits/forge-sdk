<?php

declare(strict_types=1);

namespace Tests\Unit\Enums;

use PhpDevKits\ForgeSdk\Enums\DaemonUser;

test('DaemonUser exposes root and forge',
    function (): void {
        expect(DaemonUser::Root->value)->toBe('root')
            ->and(DaemonUser::Forge->value)->toBe('forge');
    });

test('DaemonUser::tryFrom returns null for unknown users',
    function (): void {
        expect(DaemonUser::tryFrom('www-data'))->toBeNull();
    });
