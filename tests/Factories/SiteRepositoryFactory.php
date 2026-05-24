<?php

declare(strict_types=1);

namespace Tests\Factories;

use FBarrento\DataFactory\Factory;
use Override;
use PhpDevKits\ForgeSdk\Data\SiteRepository;

/**
 * @extends Factory<SiteRepository>
 */
final class SiteRepositoryFactory extends Factory
{
    /** @var class-string<SiteRepository> */
    protected string $dataObject = SiteRepository::class;

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function definition(): array
    {
        return [
            'provider' => $this->fake->randomElement(['github', 'gitlab', 'bitbucket']),
            'url' => $this->fake->bothify('org/repo-##'),
            'branch' => 'main',
            'status' => 'installed',
        ];
    }
}
