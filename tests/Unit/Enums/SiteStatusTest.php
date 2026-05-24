<?php

declare(strict_types=1);

namespace Tests\Unit\Enums;

use PhpDevKits\ForgeSdk\Enums\SiteStatus;

test('SiteStatus exposes representative lifecycle states',
    function (): void {
        expect(SiteStatus::Deployed->value)->toBe('deployed')
            ->and(SiteStatus::NeverDeployed->value)->toBe('never-deployed')
            ->and(SiteStatus::Maintenance->value)->toBe('maintenance');
    });

test('SiteStatus::tryFrom returns null for unknown states',
    function (): void {
        expect(SiteStatus::tryFrom('archived'))->toBeNull();
    });
