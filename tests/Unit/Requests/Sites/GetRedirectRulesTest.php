<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Sites;

use PhpDevKits\ForgeSdk\Data\ListRedirectRulesOptions;
use PhpDevKits\ForgeSdk\Requests\Sites\GetRedirectRules;
use Saloon\Enums\Method;

test('resolveEndpoint() interpolates the org slug, server id and site id',
    function (): void {
        $request = new GetRedirectRules('acme', 42, 7);

        expect($request->resolveEndpoint())->toBe('/orgs/acme/servers/42/sites/7/redirect-rules');
    });

test('uses the GET method',
    function (): void {
        expect(new GetRedirectRules('acme', 42, 7)->getMethod())->toBe(Method::GET);
    });

test('emits options as query parameters when supplied',
    function (): void {
        $request = new GetRedirectRules('acme', 42, 7, new ListRedirectRulesOptions(size: 5, type: 'permanent'));

        expect($request->query()->all())->toBe([
            'page[size]' => 5,
            'filter[type]' => 'permanent',
        ]);
    });
