<?php

declare(strict_types=1);

namespace Tests\Unit\Enums;

use PhpDevKits\ForgeSdk\Enums\MonitorState;

test('MonitorState exposes the alert states',
    function (): void {
        expect(MonitorState::Ok->value)->toBe('OK')
            ->and(MonitorState::Alert->value)->toBe('ALERT')
            ->and(MonitorState::Unknown->value)->toBe('UNKNOWN');
    });

test('MonitorState::tryFrom returns null for unknown states',
    function (): void {
        expect(MonitorState::tryFrom('PENDING'))->toBeNull();
    });
