<?php

declare(strict_types=1);

namespace Tests\Factories;

use DateTimeImmutable;
use FBarrento\DataFactory\Factory;
use Override;
use PhpDevKits\ForgeSdk\Data\DatabaseUser;

/**
 * @extends Factory<DatabaseUser>
 */
final class DatabaseUserFactory extends Factory
{
    /** @var class-string<DatabaseUser> */
    protected string $dataObject = DatabaseUser::class;

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function definition(): array
    {
        return [
            'id' => (string) $this->fake->numberBetween(1, 99_999),
            'name' => $this->fake->bothify('user_?#'),
            'status' => $this->fake->randomElement(['installed', 'installing', 'updating', 'removing']),
            'createdAt' => new DateTimeImmutable('-1 month'),
            'updatedAt' => new DateTimeImmutable('-1 day'),
        ];
    }
}
