<?php

declare(strict_types=1);

namespace Tests\Factories;

use DateTimeImmutable;
use FBarrento\DataFactory\Factory;
use Override;
use PhpDevKits\ForgeSdk\Data\RedirectRule;

/**
 * @extends Factory<RedirectRule>
 */
final class RedirectRuleFactory extends Factory
{
    /** @var class-string<RedirectRule> */
    protected string $dataObject = RedirectRule::class;

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function definition(): array
    {
        return [
            'id' => (string) $this->fake->numberBetween(1, 99_999),
            'from' => '/'.$this->fake->slug(),
            'to' => '/'.$this->fake->slug(),
            'type' => $this->fake->randomElement(['redirect', 'permanent']),
            'status' => $this->fake->randomElement(['installed', 'installing', 'removing']),
            'createdAt' => new DateTimeImmutable('-1 month'),
            'updatedAt' => new DateTimeImmutable('-1 day'),
        ];
    }
}
