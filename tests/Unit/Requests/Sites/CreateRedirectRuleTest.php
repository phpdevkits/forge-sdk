<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Sites;

use PhpDevKits\ForgeSdk\Data\CreateRedirectRuleData;
use PhpDevKits\ForgeSdk\Enums\RedirectType;
use PhpDevKits\ForgeSdk\Requests\Sites\CreateRedirectRule;
use Saloon\Enums\Method;

test('resolveEndpoint() interpolates the org slug, server id and site id',
    function (): void {
        $data = new CreateRedirectRuleData(from: '/a', to: '/b', type: RedirectType::Redirect);

        expect(new CreateRedirectRule('acme', 42, 7, $data)->resolveEndpoint())->toBe('/orgs/acme/servers/42/sites/7/redirect-rules');
    });

test('uses the POST method',
    function (): void {
        $data = new CreateRedirectRuleData(from: '/a', to: '/b', type: RedirectType::Redirect);

        expect(new CreateRedirectRule('acme', 42, 7, $data)->getMethod())->toBe(Method::POST);
    });

test('body() reflects the CreateRedirectRuleData payload',
    function (): void {
        $data = new CreateRedirectRuleData(from: '/old-path', to: '/new-path', type: RedirectType::Permanent);

        expect(new CreateRedirectRule('acme', 42, 7, $data)->body()->all())->toBe([
            'from' => '/old-path',
            'to' => '/new-path',
            'type' => 'permanent',
        ]);
    });
