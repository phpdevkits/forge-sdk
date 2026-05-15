<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Servers;

use PhpDevKits\ForgeSdk\Enums\ServerAction;
use PhpDevKits\ForgeSdk\Requests\Servers\SendServerAction;
use Saloon\Enums\Method;

test('resolveEndpoint() points at the /actions sub-resource',
    function (): void {
        $request = new SendServerAction('acme', 42, ServerAction::Reboot);

        expect($request->resolveEndpoint())->toBe('/orgs/acme/servers/42/actions');
    });

test('uses the POST method',
    function (): void {
        $request = new SendServerAction('acme', 42, ServerAction::Reboot);

        expect($request->getMethod())->toBe(Method::POST);
    });

test('body() contains the action slug',
    function (): void {
        expect(new SendServerAction('acme', 42, ServerAction::Reboot)->body()->all())->toBe(['action' => 'reboot'])
            ->and(new SendServerAction('acme', 42, ServerAction::PowerCycle)->body()->all())->toBe(['action' => 'power-cycle']);
    });
