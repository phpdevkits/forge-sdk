<?php

declare(strict_types=1);

namespace Tests\Factories;

use FBarrento\DataFactory\Factory;
use Override;
use PhpDevKits\ForgeSdk\Data\ProviderRegion;

/**
 * @extends Factory<ProviderRegion>
 */
final class ProviderRegionFactory extends Factory
{
    /** @var class-string<ProviderRegion> */
    protected string $dataObject = ProviderRegion::class;

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function definition(): array
    {
        return [
            'id' => (string) $this->fake->numberBetween(1, 999),
            'name' => $this->fake->city(),
            'code' => $this->fake->bothify('???#'),
            'alternateCode' => $this->fake->boolean() ? $this->fake->bothify('alt-???') : null,
        ];
    }
}
