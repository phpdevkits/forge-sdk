<?php

declare(strict_types=1);

namespace Tests\Unit\Enums;

use PhpDevKits\ForgeSdk\Enums\ServerAction;

test('ServerAction exposes the canonical actions',
    function (): void {
        expect(ServerAction::Reboot->value)->toBe('reboot')
            ->and(ServerAction::PowerCycle->value)->toBe('power-cycle');
    });

test('ServerAction::tryFrom returns null for unknown actions',
    function (): void {
        expect(ServerAction::tryFrom('shutdown'))->toBeNull();
    });
