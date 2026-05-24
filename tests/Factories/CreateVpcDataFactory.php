<?php

declare(strict_types=1);

namespace Tests\Factories;

use FBarrento\DataFactory\Factory;
use Override;
use PhpDevKits\ForgeSdk\Data\CreateVpcData;

/**
 * @extends Factory<CreateVpcData>
 */
final class CreateVpcDataFactory extends Factory
{
    /** @var class-string<CreateVpcData> */
    protected string $dataObject = CreateVpcData::class;

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function definition(): array
    {
        return [
            'name' => $this->fake->bothify('sdk-vpc-?#'),
        ];
    }
}
