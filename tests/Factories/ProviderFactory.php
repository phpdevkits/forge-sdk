<?php

declare(strict_types=1);

namespace Tests\Factories;

use FBarrento\DataFactory\Factory;
use Override;
use PhpDevKits\ForgeSdk\Data\Provider;

/**
 * @extends Factory<Provider>
 */
final class ProviderFactory extends Factory
{
    /** @var class-string<Provider> */
    protected string $dataObject = Provider::class;

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function definition(): array
    {
        $name = $this->fake->randomElement(['DigitalOcean', 'AWS', 'Linode', 'Vultr', 'Hetzner']);

        return [
            'id' => (string) $this->fake->numberBetween(1, 99_999),
            'name' => $name,
            'slug' => strtolower((string) $name),
            'simpleName' => $name,
            'currency' => 'USD',
            'currencySymbol' => '$',
            'defaultSizeCode' => $this->fake->bothify('s-?#'),
            'defaultRegionCode' => $this->fake->bothify('???#'),
        ];
    }
}
