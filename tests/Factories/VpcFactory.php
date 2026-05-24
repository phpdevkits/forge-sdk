<?php

declare(strict_types=1);

namespace Tests\Factories;

use FBarrento\DataFactory\Factory;
use Override;
use PhpDevKits\ForgeSdk\Data\Vpc;
use PhpDevKits\ForgeSdk\Data\VpcSubnet;

/**
 * @extends Factory<Vpc>
 */
final class VpcFactory extends Factory
{
    /** @var class-string<Vpc> */
    protected string $dataObject = Vpc::class;

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function definition(): array
    {
        return [
            'id' => $this->fake->bothify('vpc-########'),
            'name' => $this->fake->bothify('vpc-?#'),
            'cidrBlock' => '10.0.0.0/16',
            'subnets' => [
                new VpcSubnet(id: 'subnet-aaaa', name: 'subnet-a', cidrBlock: '10.0.1.0/24'),
            ],
            'region' => $this->fake->randomElement(['nyc3', 'fra1', 'us-east-1']),
        ];
    }
}
