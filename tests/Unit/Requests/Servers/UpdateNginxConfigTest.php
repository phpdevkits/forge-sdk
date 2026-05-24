<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Servers;

use PhpDevKits\ForgeSdk\Data\UpdateNginxConfigData;
use PhpDevKits\ForgeSdk\Requests\Servers\UpdateNginxConfig;
use Saloon\Enums\Method;

test('resolveEndpoint() points at the nginx sub-resource',
    function (): void {
        $request = new UpdateNginxConfig('acme', 42, 99, new UpdateNginxConfigData(content: 'server {}'));

        expect($request->resolveEndpoint())->toBe('/orgs/acme/servers/42/sites/99/nginx');
    });

test('uses the PUT method',
    function (): void {
        $request = new UpdateNginxConfig('acme', 42, 99, new UpdateNginxConfigData(content: 'x'));

        expect($request->getMethod())->toBe(Method::PUT);
    });

test('body() maps the content onto the config key',
    function (): void {
        $request = new UpdateNginxConfig('acme', 42, 99, new UpdateNginxConfigData(content: 'server {}'));

        expect($request->body()->all())->toBe(['config' => 'server {}']);
    });
