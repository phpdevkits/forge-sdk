<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use PhpDevKits\ForgeSdk\Forge;
use PhpDevKits\ForgeSdk\Resources\SiteResource;
use PhpDevKits\ForgeSdk\Resources\SitesResource;

test('server($id)->sites() returns a SitesResource',
    function (): void {
        expect(new Forge('test-token', 'test-org')->server(42)->sites())->toBeInstanceOf(SitesResource::class);
    });

test('server($id)->site($siteId) returns a SiteResource',
    function (): void {
        expect(new Forge('test-token', 'test-org')->server(42)->site(99))->toBeInstanceOf(SiteResource::class);
    });
