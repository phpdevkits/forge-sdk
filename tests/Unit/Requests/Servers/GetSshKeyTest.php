<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Servers;

use PhpDevKits\ForgeSdk\Requests\Servers\GetSshKey;
use Saloon\Enums\Method;

test('resolveEndpoint() interpolates the org slug, server id and key id',
    function (): void {
        expect(new GetSshKey('acme', 42, 99)->resolveEndpoint())->toBe('/orgs/acme/servers/42/ssh-keys/99');
    });

test('uses the GET method',
    function (): void {
        expect(new GetSshKey('acme', 42, 99)->getMethod())->toBe(Method::GET);
    });
