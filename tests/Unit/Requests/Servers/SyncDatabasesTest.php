<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Servers;

use PhpDevKits\ForgeSdk\Requests\Servers\SyncDatabases;
use Saloon\Enums\Method;

test('resolveEndpoint() points at the schemas/synchronizations sub-resource',
    function (): void {
        expect(new SyncDatabases('acme', 42)->resolveEndpoint())->toBe('/orgs/acme/servers/42/database/schemas/synchronizations');
    });

test('uses the POST method',
    function (): void {
        expect(new SyncDatabases('acme', 42)->getMethod())->toBe(Method::POST);
    });
