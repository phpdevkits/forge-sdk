<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Servers;

use PhpDevKits\ForgeSdk\Requests\Servers\DeleteSshKey;
use Saloon\Enums\Method;

test('resolveEndpoint() interpolates the org slug, server id and key id',
    function (): void {
        expect(new DeleteSshKey('acme', 42, 99)->resolveEndpoint())->toBe('/orgs/acme/servers/42/ssh-keys/99');
    });

test('uses the DELETE method',
    function (): void {
        expect(new DeleteSshKey('acme', 42, 99)->getMethod())->toBe(Method::DELETE);
    });
