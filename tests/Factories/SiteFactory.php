<?php

declare(strict_types=1);

namespace Tests\Factories;

use DateTimeImmutable;
use FBarrento\DataFactory\Factory;
use Override;
use PhpDevKits\ForgeSdk\Data\Site;
use PhpDevKits\ForgeSdk\Data\SiteMaintenanceMode;
use PhpDevKits\ForgeSdk\Data\SiteRepository;

/**
 * @extends Factory<Site>
 */
final class SiteFactory extends Factory
{
    /** @var class-string<Site> */
    protected string $dataObject = Site::class;

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function definition(): array
    {
        return [
            'id' => (string) $this->fake->numberBetween(1, 99_999),
            'name' => $this->fake->domainName(),
            'status' => 'installed',
            'url' => $this->fake->url(),
            'user' => 'forge',
            'https' => true,
            'webDirectory' => '/public',
            'rootDirectory' => '/home/forge/example.com',
            'aliases' => [],
            'phpVersion' => 'php84',
            'deploymentStatus' => 'finished',
            'quickDeploy' => true,
            'isolated' => false,
            'sharedPaths' => [],
            'repository' => new SiteRepository(provider: 'github', url: 'org/repo', branch: 'main', status: 'installed'),
            'database' => null,
            'maintenanceMode' => new SiteMaintenanceMode(enabled: false, status: null),
            'zeroDowntimeDeployments' => true,
            'deploymentScript' => null,
            'wildcards' => false,
            'appType' => 'laravel',
            'usesEnvoyer' => false,
            'deploymentUrl' => $this->fake->url(),
            'healthcheckUrl' => null,
            'createdAt' => new DateTimeImmutable('-1 month'),
            'updatedAt' => new DateTimeImmutable('-1 day'),
        ];
    }
}
