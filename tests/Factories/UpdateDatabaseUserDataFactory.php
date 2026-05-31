<?php

declare(strict_types=1);

namespace Tests\Factories;

use FBarrento\DataFactory\Factory;
use Override;
use PhpDevKits\ForgeSdk\Data\UpdateDatabaseUserData;

/**
 * @extends Factory<UpdateDatabaseUserData>
 */
final class UpdateDatabaseUserDataFactory extends Factory
{
    /** @var class-string<UpdateDatabaseUserData> */
    protected string $dataObject = UpdateDatabaseUserData::class;

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function definition(): array
    {
        return [
            'password' => null,
            'databaseIds' => null,
        ];
    }
}
