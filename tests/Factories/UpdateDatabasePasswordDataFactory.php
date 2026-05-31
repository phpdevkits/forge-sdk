<?php

declare(strict_types=1);

namespace Tests\Factories;

use FBarrento\DataFactory\Factory;
use Override;
use PhpDevKits\ForgeSdk\Data\UpdateDatabasePasswordData;

/**
 * @extends Factory<UpdateDatabasePasswordData>
 */
final class UpdateDatabasePasswordDataFactory extends Factory
{
    /** @var class-string<UpdateDatabasePasswordData> */
    protected string $dataObject = UpdateDatabasePasswordData::class;

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function definition(): array
    {
        return [
            'password' => 'fixture-placeholder-not-a-real-secret',
        ];
    }
}
