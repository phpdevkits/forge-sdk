<?php

declare(strict_types=1);

namespace Tests\Factories;

use FBarrento\DataFactory\Factory;
use Override;
use PhpDevKits\ForgeSdk\Data\CreateFirewallRuleData;
use PhpDevKits\ForgeSdk\Enums\RuleType;

/**
 * @extends Factory<CreateFirewallRuleData>
 */
final class CreateFirewallRuleDataFactory extends Factory
{
    /** @var class-string<CreateFirewallRuleData> */
    protected string $dataObject = CreateFirewallRuleData::class;

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function definition(): array
    {
        return [
            'name' => $this->fake->bothify('sdk-test-?#'),
            'type' => RuleType::Allow,
            'port' => null,
            'ipAddress' => null,
        ];
    }
}
