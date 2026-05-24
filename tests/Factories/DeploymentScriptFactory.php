<?php

declare(strict_types=1);

namespace Tests\Factories;

use FBarrento\DataFactory\Factory;
use Override;
use PhpDevKits\ForgeSdk\Data\DeploymentScript;

/**
 * @extends Factory<DeploymentScript>
 */
final class DeploymentScriptFactory extends Factory
{
    /** @var class-string<DeploymentScript> */
    protected string $dataObject = DeploymentScript::class;

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function definition(): array
    {
        return [
            'id' => (string) $this->fake->numberBetween(1, 99_999),
            'content' => "cd /home/forge/example.com\ngit pull origin main\ncomposer install --no-dev",
            'autoSource' => true,
        ];
    }
}
