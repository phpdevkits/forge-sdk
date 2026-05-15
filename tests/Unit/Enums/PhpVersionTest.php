<?php

declare(strict_types=1);

namespace Tests\Unit\Enums;

use PhpDevKits\ForgeSdk\Enums\PhpVersion;

test('PhpVersion exposes the canonical Forge PHP slugs',
    function (): void {
        expect(PhpVersion::Php84->value)->toBe('php84')
            ->and(PhpVersion::Php85->value)->toBe('php85')
            ->and(PhpVersion::Php56Old->value)->toBe('php56-old');
    });

test('PhpVersion::tryFrom returns null for unknown slugs',
    function (): void {
        expect(PhpVersion::tryFrom('php99'))->toBeNull();
    });
