<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use PhpDevKits\ForgeSdk\Forge;
use PhpDevKits\ForgeSdk\Resources\DaemonResource;
use PhpDevKits\ForgeSdk\Resources\DaemonsResource;

test('server($id)->daemons() returns a DaemonsResource',
    function (): void {
        expect(new Forge('test-token', 'test-org')->server(42)->daemons())->toBeInstanceOf(DaemonsResource::class);
    });

test('server($id)->daemon($daemonId) returns a DaemonResource',
    function (): void {
        expect(new Forge('test-token', 'test-org')->server(42)->daemon(7))->toBeInstanceOf(DaemonResource::class);
    });
