<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use PhpDevKits\ForgeSdk\Data\CreateMonitorData;
use PhpDevKits\ForgeSdk\Enums\MonitorMetricType;
use PhpDevKits\ForgeSdk\Enums\MonitorOperator;
use Tests\Factories\CreateMonitorDataFactory;

beforeEach(function (): void {
    $this->factory = new CreateMonitorDataFactory;
});

test('factory produces a usable CreateMonitorData',
    function (): void {
        expect($this->factory->make())->toBeInstanceOf(CreateMonitorData::class);
    });

test('toArray() emits the required fields with enums serialized',
    function (): void {
        $data = new CreateMonitorData(
            type: MonitorMetricType::CpuLoad,
            operator: MonitorOperator::GreaterThanOrEqual,
            threshold: 90.0,
            notify: 'taylor@laravel.com',
        );

        expect($data->toArray())->toBe([
            'type' => 'cpu_load',
            'operator' => 'gte',
            'threshold' => 90.0,
            'notify' => 'taylor@laravel.com',
        ]);
    });

test('toArray() emits minutes when set',
    function (): void {
        $data = new CreateMonitorData(
            type: MonitorMetricType::Disk,
            operator: MonitorOperator::LessThanOrEqual,
            threshold: 10.0,
            notify: 'ops@example.com',
            minutes: 5,
        );

        expect($data->toArray())->toBe([
            'type' => 'disk',
            'operator' => 'lte',
            'threshold' => 10.0,
            'notify' => 'ops@example.com',
            'minutes' => 5,
        ]);
    });
