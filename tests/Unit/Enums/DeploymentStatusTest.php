<?php

declare(strict_types=1);

namespace Tests\Unit\Enums;

use PhpDevKits\ForgeSdk\Enums\DeploymentStatus;

test('DeploymentStatus exposes representative states',
    function (): void {
        expect(DeploymentStatus::Finished->value)->toBe('finished')
            ->and(DeploymentStatus::FailedBuild->value)->toBe('failed-build')
            ->and(DeploymentStatus::Queued->value)->toBe('queued');
    });

test('DeploymentStatus::tryFrom returns null for unknown states',
    function (): void {
        expect(DeploymentStatus::tryFrom('rolled-back'))->toBeNull();
    });
