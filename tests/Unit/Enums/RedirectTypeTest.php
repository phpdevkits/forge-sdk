<?php

declare(strict_types=1);

namespace Tests\Unit\Enums;

use PhpDevKits\ForgeSdk\Enums\RedirectType;

test('RedirectType exposes all redirect modes',
    function (): void {
        expect(RedirectType::Redirect->value)->toBe('redirect')
            ->and(RedirectType::Permanent->value)->toBe('permanent');
    });

test('RedirectType::tryFrom returns null for unknown modes',
    function (): void {
        expect(RedirectType::tryFrom('temporary'))->toBeNull();
    });
