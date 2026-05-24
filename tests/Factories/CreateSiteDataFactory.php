<?php

declare(strict_types=1);

namespace Tests\Factories;

use FBarrento\DataFactory\Factory;
use Override;
use PhpDevKits\ForgeSdk\Data\CreateSiteData;
use PhpDevKits\ForgeSdk\Enums\SiteType;

/**
 * @extends Factory<CreateSiteData>
 */
final class CreateSiteDataFactory extends Factory
{
    /** @var class-string<CreateSiteData> */
    protected string $dataObject = CreateSiteData::class;

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function definition(): array
    {
        return [
            'type' => SiteType::Php,
            'name' => $this->fake->domainName(),
            'domainMode' => null,
            'wwwRedirectType' => null,
            'rootDirectory' => null,
            'webDirectory' => null,
            'isIsolated' => null,
            'isolatedUser' => null,
            'phpVersion' => null,
            'zeroDowntimeDeployments' => null,
            'sourceControlProvider' => null,
            'repository' => null,
            'branch' => null,
            'databaseId' => null,
            'databaseUserId' => null,
        ];
    }
}
