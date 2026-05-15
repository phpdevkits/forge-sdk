<?php

declare(strict_types=1);

namespace Tests\Factories;

use FBarrento\DataFactory\Factory;
use Override;
use PhpDevKits\ForgeSdk\Data\HetznerServerConfig;

/**
 * @extends Factory<HetznerServerConfig>
 */
final class HetznerServerConfigFactory extends Factory
{
    /** @var class-string<HetznerServerConfig> */
    protected string $dataObject = HetznerServerConfig::class;

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function definition(): array
    {
        return [
            'regionId' => $this->fake->randomElement(['fsn1', 'nbg1', 'hel1', 'ash']),
            'sizeId' => $this->fake->randomElement(['cpx11', 'cpx21', 'cpx31']),
            'networkId' => null,
            'enableDailyBackups' => false,
        ];
    }
}
