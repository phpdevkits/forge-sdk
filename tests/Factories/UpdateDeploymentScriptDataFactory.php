<?php

declare(strict_types=1);

namespace Tests\Factories;

use FBarrento\DataFactory\Factory;
use Override;
use PhpDevKits\ForgeSdk\Data\UpdateDeploymentScriptData;

/**
 * @extends Factory<UpdateDeploymentScriptData>
 */
final class UpdateDeploymentScriptDataFactory extends Factory
{
    /** @var class-string<UpdateDeploymentScriptData> */
    protected string $dataObject = UpdateDeploymentScriptData::class;

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function definition(): array
    {
        return [
            'content' => "cd /home/forge/example.com\ngit pull origin main",
            'autoSource' => null,
        ];
    }
}
