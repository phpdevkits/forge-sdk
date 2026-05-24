<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\ServerCredentials;

use PhpDevKits\ForgeSdk\Requests\ServerCredentials\GetVpc;
use Saloon\Enums\Method;

test('resolveEndpoint() interpolates the org slug, credential id, region and vpc id',
    function (): void {
        $request = new GetVpc('acme', 42, 'us-east-1', 'vpc-123');

        expect($request->resolveEndpoint())->toBe('/orgs/acme/server-credentials/42/regions/us-east-1/vpcs/vpc-123');
    });

test('uses the GET method',
    function (): void {
        expect(new GetVpc('acme', 42, 'us-east-1', 'vpc-123')->getMethod())->toBe(Method::GET);
    });
