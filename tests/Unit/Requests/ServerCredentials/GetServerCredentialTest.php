<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\ServerCredentials;

use PhpDevKits\ForgeSdk\Requests\ServerCredentials\GetServerCredential;
use Saloon\Enums\Method;

test('resolveEndpoint() interpolates the organization slug and credential id',
    function (): void {
        $request = new GetServerCredential('acme', 42);

        expect($request->resolveEndpoint())->toBe('/orgs/acme/server-credentials/42');
    });

test('resolveEndpoint() accepts a string credential id',
    function (): void {
        $request = new GetServerCredential('acme', 'aws-prod');

        expect($request->resolveEndpoint())->toBe('/orgs/acme/server-credentials/aws-prod');
    });

test('uses the GET method',
    function (): void {
        expect(new GetServerCredential('acme', 42)->getMethod())->toBe(Method::GET);
    });
