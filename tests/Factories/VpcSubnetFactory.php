<?php

declare(strict_types=1);

namespace Tests\Factories;

use FBarrento\DataFactory\Factory;
use Override;
use PhpDevKits\ForgeSdk\Data\VpcSubnet;

/**
 * @extends Factory<VpcSubnet>
 */
final class VpcSubnetFactory extends Factory
{
    /** @var class-string<VpcSubnet> */
    protected string $dataObject = VpcSubnet::class;

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function definition(): array
    {
        return [
            'id' => $this->fake->bothify('subnet-########'),
            'name' => $this->fake->bothify('subnet-?#'),
            'cidrBlock' => '10.0.1.0/24',
        ];
    }
}
