<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Servers;

use PhpDevKits\ForgeSdk\Requests\Servers\GetBackupConfiguration;
use Saloon\Enums\Method;

test('resolveEndpoint() interpolates the org slug, server id and backup configuration id',
    function (): void {
        expect(new GetBackupConfiguration('acme', 42, 99)->resolveEndpoint())->toBe('/orgs/acme/servers/42/database/backups/99');
    });

test('uses the GET method',
    function (): void {
        expect(new GetBackupConfiguration('acme', 42, 99)->getMethod())->toBe(Method::GET);
    });
