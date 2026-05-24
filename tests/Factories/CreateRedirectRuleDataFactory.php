<?php

declare(strict_types=1);

namespace Tests\Factories;

use FBarrento\DataFactory\Factory;
use Override;
use PhpDevKits\ForgeSdk\Data\CreateRedirectRuleData;
use PhpDevKits\ForgeSdk\Enums\RedirectType;

/**
 * @extends Factory<CreateRedirectRuleData>
 */
final class CreateRedirectRuleDataFactory extends Factory
{
    /** @var class-string<CreateRedirectRuleData> */
    protected string $dataObject = CreateRedirectRuleData::class;

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function definition(): array
    {
        return [
            'from' => '/old-path',
            'to' => '/new-path',
            'type' => $this->fake->randomElement([RedirectType::Redirect, RedirectType::Permanent]),
        ];
    }
}
