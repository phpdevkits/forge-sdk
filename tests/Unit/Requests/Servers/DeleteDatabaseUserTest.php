<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Servers;

use PhpDevKits\ForgeSdk\Requests\Servers\DeleteDatabaseUser;
use Saloon\Enums\Method;

test('resolveEndpoint() interpolates the org slug, server id and database user id',
    function (): void {
        expect(new DeleteDatabaseUser('acme', 42, 99)->resolveEndpoint())->toBe('/orgs/acme/servers/42/database/users/99');
    });

test('uses the DELETE method',
    function (): void {
        expect(new DeleteDatabaseUser('acme', 42, 99)->getMethod())->toBe(Method::DELETE);
    });
