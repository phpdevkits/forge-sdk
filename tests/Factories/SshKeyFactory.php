<?php

declare(strict_types=1);

namespace Tests\Factories;

use DateTimeImmutable;
use FBarrento\DataFactory\Factory;
use Override;
use PhpDevKits\ForgeSdk\Data\SshKey;

/**
 * @extends Factory<SshKey>
 */
final class SshKeyFactory extends Factory
{
    /** @var class-string<SshKey> */
    protected string $dataObject = SshKey::class;

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function definition(): array
    {
        return [
            'id' => (string) $this->fake->numberBetween(1, 99_999),
            'name' => $this->fake->bothify('?#-laptop'),
            'user' => 'forge',
            'status' => $this->fake->randomElement(['installed', 'installing']),
            'createdBy' => $this->fake->numberBetween(1, 9_999),
            'createdAt' => new DateTimeImmutable('-1 month'),
            'updatedAt' => new DateTimeImmutable('-1 day'),
        ];
    }
}
