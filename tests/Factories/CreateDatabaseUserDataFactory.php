<?php

declare(strict_types=1);

namespace Tests\Factories;

use FBarrento\DataFactory\Factory;
use Override;
use PhpDevKits\ForgeSdk\Data\CreateDatabaseUserData;

/**
 * @extends Factory<CreateDatabaseUserData>
 */
final class CreateDatabaseUserDataFactory extends Factory
{
    /** @var class-string<CreateDatabaseUserData> */
    protected string $dataObject = CreateDatabaseUserData::class;

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function definition(): array
    {
        return [
            'name' => $this->fake->bothify('sdk_user_?#'),
            'password' => $this->fake->bothify('pw-########'),
            'readOnly' => null,
            'databaseIds' => null,
        ];
    }
}
