<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Servers;

use PhpDevKits\ForgeSdk\Requests\Servers\TriggerBackup;
use Saloon\Enums\Method;

test('resolveEndpoint() posts to the instances collection',
    function (): void {
        expect(new TriggerBackup('acme', 42, 7)->resolveEndpoint())
            ->toBe('/orgs/acme/servers/42/database/backups/7/instances');
    });

test('uses the POST method',
    function (): void {
        expect(new TriggerBackup('acme', 42, 7)->getMethod())->toBe(Method::POST);
    });
