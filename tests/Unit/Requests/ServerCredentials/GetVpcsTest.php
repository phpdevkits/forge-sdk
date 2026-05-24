<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\ServerCredentials;

use PhpDevKits\ForgeSdk\Requests\ServerCredentials\GetVpcs;
use Saloon\Enums\Method;

test('resolveEndpoint() interpolates the org slug, credential id and region',
    function (): void {
        $request = new GetVpcs('acme', 42, 'us-east-1');

        expect($request->resolveEndpoint())->toBe('/orgs/acme/server-credentials/42/regions/us-east-1/vpcs');
    });

test('resolveEndpoint() accepts a string credential id',
    function (): void {
        $request = new GetVpcs('acme', 'aws-prod', 'fra1');

        expect($request->resolveEndpoint())->toBe('/orgs/acme/server-credentials/aws-prod/regions/fra1/vpcs');
    });

test('uses the GET method',
    function (): void {
        expect(new GetVpcs('acme', 42, 'us-east-1')->getMethod())->toBe(Method::GET);
    });
