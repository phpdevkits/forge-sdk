<?php

declare(strict_types=1);

namespace Tests\Unit\Enums;

use PhpDevKits\ForgeSdk\Enums\MonitorOperator;

test('MonitorOperator exposes the comparison slugs',
    function (): void {
        expect(MonitorOperator::GreaterThanOrEqual->value)->toBe('gte')
            ->and(MonitorOperator::LessThanOrEqual->value)->toBe('lte');
    });

test('MonitorOperator::tryFrom returns null for unknown operators',
    function (): void {
        expect(MonitorOperator::tryFrom('eq'))->toBeNull();
    });
