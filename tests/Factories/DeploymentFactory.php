<?php

declare(strict_types=1);

namespace Tests\Factories;

use DateTimeImmutable;
use FBarrento\DataFactory\Factory;
use Override;
use PhpDevKits\ForgeSdk\Data\Deployment;
use PhpDevKits\ForgeSdk\Data\DeploymentCommit;

/**
 * @extends Factory<Deployment>
 */
final class DeploymentFactory extends Factory
{
    /** @var class-string<Deployment> */
    protected string $dataObject = Deployment::class;

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function definition(): array
    {
        return [
            'id' => (string) $this->fake->numberBetween(1, 99_999),
            'commit' => new DeploymentCommit(
                hash: $this->fake->sha1(),
                author: $this->fake->name(),
                message: $this->fake->sentence(),
                branch: 'main',
            ),
            'type' => 'deployment',
            'status' => 'finished',
            'startedAt' => new DateTimeImmutable('-10 minutes'),
            'endedAt' => new DateTimeImmutable('-9 minutes'),
            'createdAt' => new DateTimeImmutable('-10 minutes'),
            'updatedAt' => new DateTimeImmutable('-9 minutes'),
        ];
    }
}
