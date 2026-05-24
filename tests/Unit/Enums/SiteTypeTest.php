<?php

declare(strict_types=1);

namespace Tests\Unit\Enums;

use PhpDevKits\ForgeSdk\Enums\SiteType;

test('SiteType exposes representative project types',
    function (): void {
        expect(SiteType::Laravel->value)->toBe('laravel')
            ->and(SiteType::StaticHtml->value)->toBe('static-html')
            ->and(SiteType::PhpMyAdmin->value)->toBe('phpmyadmin')
            ->and(SiteType::NextJs->value)->toBe('nextjs');
    });

test('SiteType::tryFrom returns null for unknown types',
    function (): void {
        expect(SiteType::tryFrom('django'))->toBeNull();
    });
