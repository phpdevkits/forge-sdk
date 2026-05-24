<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Servers;

use PhpDevKits\ForgeSdk\Data\UpdateDeploymentScriptData;
use PhpDevKits\ForgeSdk\Requests\Servers\UpdateDeploymentScript;
use Saloon\Enums\Method;

test('resolveEndpoint() points at the deployments/script sub-resource',
    function (): void {
        $request = new UpdateDeploymentScript('acme', 42, 99, new UpdateDeploymentScriptData(content: 'git pull'));

        expect($request->resolveEndpoint())->toBe('/orgs/acme/servers/42/sites/99/deployments/script');
    });

test('uses the PUT method',
    function (): void {
        $request = new UpdateDeploymentScript('acme', 42, 99, new UpdateDeploymentScriptData(content: 'x'));

        expect($request->getMethod())->toBe(Method::PUT);
    });

test('body() reflects the UpdateDeploymentScriptData payload',
    function (): void {
        $request = new UpdateDeploymentScript('acme', 42, 99, new UpdateDeploymentScriptData(content: 'git pull', autoSource: true));

        expect($request->body()->all())->toBe(['content' => 'git pull', 'auto_source' => true]);
    });
