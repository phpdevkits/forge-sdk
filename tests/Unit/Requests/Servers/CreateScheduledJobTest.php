<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Servers;

use PhpDevKits\ForgeSdk\Data\CreateScheduledJobData;
use PhpDevKits\ForgeSdk\Enums\CronFrequency;
use PhpDevKits\ForgeSdk\Requests\Servers\CreateScheduledJob;
use Saloon\Enums\Method;

test('resolveEndpoint() posts to the scheduled-jobs collection',
    function (): void {
        $data = new CreateScheduledJobData(command: 'echo hi', user: 'root', frequency: CronFrequency::Hourly);

        expect(new CreateScheduledJob('acme', 42, $data)->resolveEndpoint())->toBe('/orgs/acme/servers/42/scheduled-jobs');
    });

test('uses the POST method',
    function (): void {
        $data = new CreateScheduledJobData(command: 'echo hi', user: 'root', frequency: CronFrequency::Hourly);

        expect(new CreateScheduledJob('acme', 42, $data)->getMethod())->toBe(Method::POST);
    });

test('body() reflects a minimal CreateScheduledJobData payload',
    function (): void {
        $data = new CreateScheduledJobData(command: 'echo hi', user: 'root', frequency: CronFrequency::Hourly);

        expect(new CreateScheduledJob('acme', 42, $data)->body()->all())->toBe([
            'command' => 'echo hi',
            'user' => 'root',
            'frequency' => 'hourly',
        ]);
    });

test('body() reflects a full custom-cron payload',
    function (): void {
        $data = new CreateScheduledJobData(
            command: 'php artisan schedule:run',
            user: 'forge',
            frequency: CronFrequency::Custom,
            name: 'Scheduler',
            cron: '0 * * * *',
            heartbeat: true,
            gracePeriod: 5,
        );

        expect(new CreateScheduledJob('acme', 42, $data)->body()->all())->toBe([
            'command' => 'php artisan schedule:run',
            'user' => 'forge',
            'frequency' => 'custom',
            'name' => 'Scheduler',
            'cron' => '0 * * * *',
            'heartbeat' => true,
            'grace_period' => 5,
        ]);
    });
