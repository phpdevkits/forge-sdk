<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Servers;

use PhpDevKits\ForgeSdk\Enums\DaemonAction;
use PhpDevKits\ForgeSdk\Requests\Servers\SendDaemonAction;
use Saloon\Enums\Method;

test('resolveEndpoint() points at the daemon actions sub-resource',
    function (): void {
        expect(new SendDaemonAction('acme', 42, 7, DaemonAction::Restart)->resolveEndpoint())
            ->toBe('/orgs/acme/servers/42/background-processes/7/actions');
    });

test('uses the POST method',
    function (): void {
        expect(new SendDaemonAction('acme', 42, 7, DaemonAction::Restart)->getMethod())->toBe(Method::POST);
    });

test('body() contains the action slug',
    function (): void {
        expect(new SendDaemonAction('acme', 42, 7, DaemonAction::Restart)->body()->all())->toBe(['action' => 'restart'])
            ->and(new SendDaemonAction('acme', 42, 7, DaemonAction::EmptyLog)->body()->all())->toBe(['action' => 'empty-log']);
    });
