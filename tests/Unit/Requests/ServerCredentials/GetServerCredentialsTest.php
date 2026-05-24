<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\ServerCredentials;

use PhpDevKits\ForgeSdk\Data\ListServerCredentialsOptions;
use PhpDevKits\ForgeSdk\Requests\ServerCredentials\GetServerCredentials;
use Saloon\Enums\Method;

test('resolveEndpoint() interpolates the organization slug',
    function (): void {
        $request = new GetServerCredentials('acme');

        expect($request->resolveEndpoint())->toBe('/orgs/acme/server-credentials');
    });

test('uses the GET method',
    function (): void {
        expect(new GetServerCredentials('acme')->getMethod())->toBe(Method::GET);
    });

test('emits no query parameters when no options are supplied',
    function (): void {
        expect(new GetServerCredentials('acme')->query()->all())->toBe([]);
    });

test('emits pagination query parameters when options are supplied',
    function (): void {
        $request = new GetServerCredentials('acme', new ListServerCredentialsOptions(size: 5, cursor: 'CURSOR-A'));

        expect($request->query()->all())->toBe([
            'page[size]' => 5,
            'page[cursor]' => 'CURSOR-A',
        ]);
    });
