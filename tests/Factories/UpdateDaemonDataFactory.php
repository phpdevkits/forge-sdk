<?php

declare(strict_types=1);

namespace Tests\Factories;

use FBarrento\DataFactory\Factory;
use Override;
use PhpDevKits\ForgeSdk\Data\UpdateDaemonData;

/**
 * @extends Factory<UpdateDaemonData>
 */
final class UpdateDaemonDataFactory extends Factory
{
    /** @var class-string<UpdateDaemonData> */
    protected string $dataObject = UpdateDaemonData::class;

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function definition(): array
    {
        return [
            'name' => $this->fake->bothify('worker-##'),
            'config' => null,
        ];
    }
}
