<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Servers;

use PhpDevKits\ForgeSdk\Data\CreateMonitorData;
use PhpDevKits\ForgeSdk\Enums\MonitorMetricType;
use PhpDevKits\ForgeSdk\Enums\MonitorOperator;
use PhpDevKits\ForgeSdk\Requests\Servers\CreateMonitor;
use Saloon\Enums\Method;

test('resolveEndpoint() interpolates the org slug and server id',
    function (): void {
        $data = new CreateMonitorData(
            type: MonitorMetricType::CpuLoad,
            operator: MonitorOperator::GreaterThanOrEqual,
            threshold: 90.0,
            notify: 'taylor@laravel.com',
        );

        expect(new CreateMonitor('acme', 42, $data)->resolveEndpoint())->toBe('/orgs/acme/servers/42/monitors');
    });

test('uses the POST method',
    function (): void {
        $data = new CreateMonitorData(
            type: MonitorMetricType::CpuLoad,
            operator: MonitorOperator::GreaterThanOrEqual,
            threshold: 90.0,
            notify: 'taylor@laravel.com',
        );

        expect(new CreateMonitor('acme', 42, $data)->getMethod())->toBe(Method::POST);
    });

test('body() reflects the CreateMonitorData payload',
    function (): void {
        $data = new CreateMonitorData(
            type: MonitorMetricType::Disk,
            operator: MonitorOperator::LessThanOrEqual,
            threshold: 10.0,
            notify: 'ops@example.com',
            minutes: 5,
        );

        expect(new CreateMonitor('acme', 42, $data)->body()->all())->toBe([
            'type' => 'disk',
            'operator' => 'lte',
            'threshold' => 10.0,
            'notify' => 'ops@example.com',
            'minutes' => 5,
        ]);
    });
