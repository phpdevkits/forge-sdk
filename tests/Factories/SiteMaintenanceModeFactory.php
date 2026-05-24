<?php

declare(strict_types=1);

namespace Tests\Factories;

use FBarrento\DataFactory\Factory;
use Override;
use PhpDevKits\ForgeSdk\Data\SiteMaintenanceMode;

/**
 * @extends Factory<SiteMaintenanceMode>
 */
final class SiteMaintenanceModeFactory extends Factory
{
    /** @var class-string<SiteMaintenanceMode> */
    protected string $dataObject = SiteMaintenanceMode::class;

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function definition(): array
    {
        return [
            'enabled' => false,
            'status' => null,
        ];
    }
}
