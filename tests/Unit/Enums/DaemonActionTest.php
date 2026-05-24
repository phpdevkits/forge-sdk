<?php

declare(strict_types=1);

namespace Tests\Unit\Enums;

use PhpDevKits\ForgeSdk\Enums\DaemonAction;

test('DaemonAction exposes all actions',
    function (): void {
        expect(DaemonAction::Restart->value)->toBe('restart')
            ->and(DaemonAction::Stop->value)->toBe('stop')
            ->and(DaemonAction::Start->value)->toBe('start')
            ->and(DaemonAction::EmptyLog->value)->toBe('empty-log');
    });

test('DaemonAction::tryFrom returns null for unknown actions',
    function (): void {
        expect(DaemonAction::tryFrom('pause'))->toBeNull();
    });
