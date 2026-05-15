<?php

declare(strict_types=1);

namespace Tests\Unit\Enums;

use PhpDevKits\ForgeSdk\Enums\UbuntuVersion;

test('UbuntuVersion exposes both supported releases',
    function (): void {
        expect(UbuntuVersion::Ubuntu2204->value)->toBe('22.04')
            ->and(UbuntuVersion::Ubuntu2404->value)->toBe('24.04');
    });

test('UbuntuVersion::tryFrom returns null for unsupported releases',
    function (): void {
        expect(UbuntuVersion::tryFrom('20.04'))->toBeNull();
    });
