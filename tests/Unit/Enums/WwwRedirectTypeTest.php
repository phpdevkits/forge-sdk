<?php

declare(strict_types=1);

namespace Tests\Unit\Enums;

use PhpDevKits\ForgeSdk\Enums\WwwRedirectType;

test('WwwRedirectType exposes all redirect modes',
    function (): void {
        expect(WwwRedirectType::FromWww->value)->toBe('from-www')
            ->and(WwwRedirectType::ToWww->value)->toBe('to-www')
            ->and(WwwRedirectType::None->value)->toBe('none');
    });

test('WwwRedirectType::tryFrom returns null for unknown modes',
    function (): void {
        expect(WwwRedirectType::tryFrom('both'))->toBeNull();
    });
