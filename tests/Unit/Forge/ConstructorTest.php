<?php

declare(strict_types=1);

namespace Tests\Unit\Forge;

use PhpDevKits\ForgeSdk\Forge;

test('constructor captures the default organization slug',
    function (): void {
        $forge = new Forge('test-token', 'acme');

        expect($forge->defaultOrganization)->toBe('acme');
    });

test('constructor leaves defaultOrganization null when no slug is given',
    function (): void {
        $forge = new Forge('test-token');

        expect($forge->defaultOrganization)->toBeNull();
    });
