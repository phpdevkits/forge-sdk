<?php

declare(strict_types=1);

namespace Tests\Factories;

use FBarrento\DataFactory\Factory;
use Override;
use PhpDevKits\ForgeSdk\Data\UpdateServerData;

/**
 * @extends Factory<UpdateServerData>
 */
final class UpdateServerDataFactory extends Factory
{
    /** @var class-string<UpdateServerData> */
    protected string $dataObject = UpdateServerData::class;

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function definition(): array
    {
        return [
            'name' => $this->fake->bothify('renamed-##'),
            'ipAddress' => null,
            'privateIpAddress' => null,
            'timezone' => null,
            'tags' => null,
        ];
    }
}
