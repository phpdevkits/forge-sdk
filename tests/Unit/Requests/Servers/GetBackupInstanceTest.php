<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Servers;

use PhpDevKits\ForgeSdk\Requests\Servers\GetBackupInstance;
use Saloon\Enums\Method;

test('resolveEndpoint() interpolates the backup id',
    function (): void {
        expect(new GetBackupInstance('acme', 42, 7, 99)->resolveEndpoint())
            ->toBe('/orgs/acme/servers/42/database/backups/7/instances/99');
    });

test('uses the GET method',
    function (): void {
        expect(new GetBackupInstance('acme', 42, 7, 99)->getMethod())->toBe(Method::GET);
    });
