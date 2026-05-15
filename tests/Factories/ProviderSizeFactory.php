<?php

declare(strict_types=1);

namespace Tests\Factories;

use FBarrento\DataFactory\Factory;
use Override;
use PhpDevKits\ForgeSdk\Data\ProviderSize;

/**
 * @extends Factory<ProviderSize>
 */
final class ProviderSizeFactory extends Factory
{
    /** @var class-string<ProviderSize> */
    protected string $dataObject = ProviderSize::class;

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function definition(): array
    {
        return [
            'id' => (string) $this->fake->numberBetween(1, 9999),
            'name' => $this->fake->bothify('Size ?#'),
            'code' => $this->fake->bothify('s-?#vcpu-#gb'),
            'series' => $this->fake->randomElement(['general', 'compute', 'memory']),
            'category' => $this->fake->randomElement(['standard', 'premium']),
            'cpus' => $this->fake->randomElement([1, 2, 4, 8, 16]),
            'diskType' => $this->fake->randomElement(['ssd', 'nvme']),
            'architecture' => null,
            'ram' => $this->fake->randomElement([1024, 2048, 4096, 8192, 16384]),
            'disk' => $this->fake->randomElement([25_600, 51_200, 102_400, 256_000]),
        ];
    }
}
