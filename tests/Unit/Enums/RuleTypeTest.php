<?php

declare(strict_types=1);

namespace Tests\Unit\Enums;

use PhpDevKits\ForgeSdk\Enums\RuleType;

test('RuleType exposes all types',
    function (): void {
        expect(RuleType::Allow->value)->toBe('allow')
            ->and(RuleType::Deny->value)->toBe('deny');
    });

test('RuleType::tryFrom returns null for unknown types',
    function (): void {
        expect(RuleType::tryFrom('reject'))->toBeNull();
    });
