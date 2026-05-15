<?php

declare(strict_types=1);

namespace Tests\Factories;

use DateTimeImmutable;
use FBarrento\DataFactory\Factory;
use Override;
use PhpDevKits\ForgeSdk\Data\Organization;

/**
 * @extends Factory<Organization>
 */
final class OrganizationFactory extends Factory
{
    /** @var class-string<Organization> */
    protected string $dataObject = Organization::class;

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function definition(): array
    {
        return [
            'id' => (string) $this->fake->numberBetween(1, 99_999),
            'name' => $this->fake->company(),
            'slug' => $this->fake->slug(2),
            'createdAt' => new DateTimeImmutable('-1 year'),
            'updatedAt' => new DateTimeImmutable('-1 day'),
        ];
    }
}
