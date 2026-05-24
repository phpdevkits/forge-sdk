<?php

declare(strict_types=1);

namespace Tests\Factories;

use DateTimeImmutable;
use FBarrento\DataFactory\Factory;
use Override;
use PhpDevKits\ForgeSdk\Data\Database;

/**
 * @extends Factory<Database>
 */
final class DatabaseFactory extends Factory
{
    /** @var class-string<Database> */
    protected string $dataObject = Database::class;

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function definition(): array
    {
        return [
            'id' => (string) $this->fake->numberBetween(1, 99_999),
            'name' => $this->fake->bothify('db_?#'),
            'status' => $this->fake->randomElement(['installed', 'installing', 'removing']),
            'createdAt' => new DateTimeImmutable('-1 month'),
            'updatedAt' => new DateTimeImmutable('-1 day'),
        ];
    }
}
