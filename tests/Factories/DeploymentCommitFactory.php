<?php

declare(strict_types=1);

namespace Tests\Factories;

use FBarrento\DataFactory\Factory;
use Override;
use PhpDevKits\ForgeSdk\Data\DeploymentCommit;

/**
 * @extends Factory<DeploymentCommit>
 */
final class DeploymentCommitFactory extends Factory
{
    /** @var class-string<DeploymentCommit> */
    protected string $dataObject = DeploymentCommit::class;

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function definition(): array
    {
        return [
            'hash' => $this->fake->sha1(),
            'author' => $this->fake->name(),
            'message' => $this->fake->sentence(),
            'branch' => 'main',
        ];
    }
}
