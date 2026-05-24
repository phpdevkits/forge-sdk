<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Servers;

use PhpDevKits\ForgeSdk\Requests\Servers\DeleteDatabase;
use Saloon\Enums\Method;

test('resolveEndpoint() interpolates the org slug, server id and database id',
    function (): void {
        expect(new DeleteDatabase('acme', 42, 99)->resolveEndpoint())->toBe('/orgs/acme/servers/42/database/schemas/99');
    });

test('uses the DELETE method',
    function (): void {
        expect(new DeleteDatabase('acme', 42, 99)->getMethod())->toBe(Method::DELETE);
    });
