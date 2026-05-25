<?php

declare(strict_types=1);

namespace Tests\Unit\Enums;

use PhpDevKits\ForgeSdk\Enums\SiteLog;

test('SiteLog exposes the nginx log segments',
    function (): void {
        expect(SiteLog::NginxAccess->value)->toBe('nginx-access')
            ->and(SiteLog::NginxError->value)->toBe('nginx-error');
    });

test('SiteLog::tryFrom returns null for unknown logs',
    function (): void {
        expect(SiteLog::tryFrom('php-fpm'))->toBeNull();
    });
