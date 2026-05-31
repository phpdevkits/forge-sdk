<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Servers;

use PhpDevKits\ForgeSdk\Data\UpdateDatabasePasswordData;
use PhpDevKits\ForgeSdk\Requests\Servers\UpdateDatabasePassword;
use Saloon\Enums\Method;

test('resolveEndpoint() points at the server database password sub-resource',
    function (): void {
        $request = new UpdateDatabasePassword('acme', 42, new UpdateDatabasePasswordData(password: 'placeholder'));

        expect($request->resolveEndpoint())->toBe('/orgs/acme/servers/42/database/password');
    });

test('resolveEndpoint() accepts string server ids',
    function (): void {
        $request = new UpdateDatabasePassword('acme', 'srv-42', new UpdateDatabasePasswordData(password: 'placeholder'));

        expect($request->resolveEndpoint())->toBe('/orgs/acme/servers/srv-42/database/password');
    });

test('uses the PUT method',
    function (): void {
        $request = new UpdateDatabasePassword('acme', 42, new UpdateDatabasePasswordData(password: 'placeholder'));

        expect($request->getMethod())->toBe(Method::PUT);
    });

test('body() reflects the UpdateDatabasePasswordData payload',
    function (): void {
        $request = new UpdateDatabasePassword('acme', 42, new UpdateDatabasePasswordData(password: 'placeholder-value'));

        expect($request->body()->all())->toBe(['password' => 'placeholder-value']);
    });
