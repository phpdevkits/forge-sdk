<?php

declare(strict_types=1);

namespace Tests\Factories;

use FBarrento\DataFactory\Factory;
use Override;
use PhpDevKits\ForgeSdk\Data\UpdateSiteData;

/**
 * @extends Factory<UpdateSiteData>
 */
final class UpdateSiteDataFactory extends Factory
{
    /** @var class-string<UpdateSiteData> */
    protected string $dataObject = UpdateSiteData::class;

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function definition(): array
    {
        return [
            'rootPath' => null,
            'directory' => null,
            'type' => null,
            'phpVersion' => null,
            'pushToDeploy' => null,
            'repositoryBranch' => null,
        ];
    }
}
