<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use InvalidArgumentException;
use PhpDevKits\ForgeSdk\Data\SiteMaintenanceMode;
use Tests\Factories\SiteMaintenanceModeFactory;

beforeEach(function (): void {
    $this->factory = new SiteMaintenanceModeFactory;
});

test('factory produces a usable SiteMaintenanceMode',
    function (): void {
        expect($this->factory->make())->toBeInstanceOf(SiteMaintenanceMode::class);
    });

test('::from() hydrates enabled + status',
    function (): void {
        $mode = SiteMaintenanceMode::from(['enabled' => true, 'status' => 'enabling']);

        expect($mode->enabled)->toBeTrue()
            ->and($mode->status)->toBe('enabling');
    });

test('::from() accepts null status',
    function (): void {
        expect(SiteMaintenanceMode::from(['enabled' => false, 'status' => null])->status)->toBeNull();
    });

test('::from() accepts null enabled (fresh-site case)',
    function (): void {
        expect(SiteMaintenanceMode::from(['enabled' => null, 'status' => null])->enabled)->toBeNull();
    });

test('::from() throws when enabled is the wrong type',
    function (): void {
        SiteMaintenanceMode::from(['enabled' => 'yes', 'status' => null]);
    })->throws(InvalidArgumentException::class, '`enabled` must be a boolean or null');

test('::from() throws when status is the wrong type',
    function (): void {
        SiteMaintenanceMode::from(['enabled' => false, 'status' => 1]);
    })->throws(InvalidArgumentException::class, '`status` must be a string or null');

test('jsonSerialize() round-trips both fields',
    function (): void {
        expect(new SiteMaintenanceMode(enabled: true, status: 'disabling')->jsonSerialize())->toBe([
            'enabled' => true,
            'status' => 'disabling',
        ]);
    });
