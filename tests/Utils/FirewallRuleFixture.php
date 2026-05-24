<?php

declare(strict_types=1);

namespace Tests\Utils;

use Override;

/**
 * Saloon fixture for the firewall-rule endpoints. Inherits the shared
 * redactions in {@see ForgeFixture}, scrubs `ip_address` (which can reveal
 * real infrastructure addresses), and stabilizes pagination cursors.
 */
final class FirewallRuleFixture extends ForgeFixture
{
    /**
     * @return array<string, string|int|callable>
     */
    #[Override]
    protected function defineSensitiveJsonParameters(): array
    {
        $stableCursor = static fn (mixed $value): ?string => $value === null ? null : 'CURSOR-A';

        return [
            ...parent::defineSensitiveJsonParameters(),
            'next_cursor' => $stableCursor,
            'prev_cursor' => $stableCursor,
            'ip_address' => '203.0.113.10',
        ];
    }
}
