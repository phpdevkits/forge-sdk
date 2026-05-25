<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Servers;

use PhpDevKits\ForgeSdk\Requests\Servers\DeleteServerLog;
use Saloon\Enums\Method;

test('resolveEndpoint() interpolates the org, server and log key',
    function (): void {
        expect(new DeleteServerLog('acme', 42, 'nginx')->resolveEndpoint())->toBe('/orgs/acme/servers/42/logs/nginx');
    });

test('uses the DELETE method',
    function (): void {
        expect(new DeleteServerLog('acme', 42, 'nginx')->getMethod())->toBe(Method::DELETE);
    });
