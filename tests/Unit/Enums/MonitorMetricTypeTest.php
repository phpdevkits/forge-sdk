<?php

declare(strict_types=1);

namespace Tests\Unit\Enums;

use PhpDevKits\ForgeSdk\Enums\MonitorMetricType;

test('MonitorMetricType exposes the metric slugs',
    function (): void {
        expect(MonitorMetricType::CpuLoad->value)->toBe('cpu_load')
            ->and(MonitorMetricType::Disk->value)->toBe('disk')
            ->and(MonitorMetricType::FreeMemory->value)->toBe('free_memory')
            ->and(MonitorMetricType::UsedMemory->value)->toBe('used_memory');
    });

test('MonitorMetricType::tryFrom returns null for unknown metrics',
    function (): void {
        expect(MonitorMetricType::tryFrom('network'))->toBeNull();
    });
