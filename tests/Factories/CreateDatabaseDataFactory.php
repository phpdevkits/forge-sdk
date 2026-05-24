<?php

declare(strict_types=1);

namespace Tests\Factories;

use FBarrento\DataFactory\Factory;
use Override;
use PhpDevKits\ForgeSdk\Data\CreateDatabaseData;

/**
 * @extends Factory<CreateDatabaseData>
 */
final class CreateDatabaseDataFactory extends Factory
{
    /** @var class-string<CreateDatabaseData> */
    protected string $dataObject = CreateDatabaseData::class;

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function definition(): array
    {
        return [
            'name' => $this->fake->bothify('sdk_db_?#'),
            'user' => null,
            'password' => null,
        ];
    }
}
