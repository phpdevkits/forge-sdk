<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\ServerCredentials;

use PhpDevKits\ForgeSdk\Data\CreateVpcData;
use PhpDevKits\ForgeSdk\Requests\ServerCredentials\CreateVpc;
use Saloon\Enums\Method;

test('resolveEndpoint() interpolates the org slug, credential id and region',
    function (): void {
        $request = new CreateVpc('acme', 42, 'us-east-1', new CreateVpcData(name: 'my-vpc'));

        expect($request->resolveEndpoint())->toBe('/orgs/acme/server-credentials/42/regions/us-east-1/vpcs');
    });

test('uses the POST method',
    function (): void {
        expect(new CreateVpc('acme', 42, 'us-east-1', new CreateVpcData(name: 'my-vpc'))->getMethod())->toBe(Method::POST);
    });

test('body() reflects the CreateVpcData payload',
    function (): void {
        $request = new CreateVpc('acme', 42, 'us-east-1', new CreateVpcData(name: 'my-vpc'));

        expect($request->body()->all())->toBe([
            'name' => 'my-vpc',
        ]);
    });
