<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Servers;

use PhpDevKits\ForgeSdk\Data\UpdateEnvironmentData;
use PhpDevKits\ForgeSdk\Requests\Servers\UpdateEnvironment;
use Saloon\Enums\Method;

test('resolveEndpoint() points at the environment sub-resource',
    function (): void {
        $request = new UpdateEnvironment('acme', 42, 99, new UpdateEnvironmentData(content: 'APP_ENV=production'));

        expect($request->resolveEndpoint())->toBe('/orgs/acme/servers/42/sites/99/environment');
    });

test('uses the PUT method',
    function (): void {
        $request = new UpdateEnvironment('acme', 42, 99, new UpdateEnvironmentData(content: 'x'));

        expect($request->getMethod())->toBe(Method::PUT);
    });

test('body() reflects the UpdateEnvironmentData payload',
    function (): void {
        $request = new UpdateEnvironment('acme', 42, 99, new UpdateEnvironmentData(content: 'APP_ENV=production', cache: true));

        expect($request->body()->all())->toBe(['environment' => 'APP_ENV=production', 'cache' => true]);
    });
