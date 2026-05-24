<?php

declare(strict_types=1);

namespace Tests\Utils;

use Override;

/**
 * Saloon fixture for the site redirect-rule endpoints. Inherits the shared
 * redactions in {@see ForgeFixture} and stabilizes pagination cursors.
 */
final class RedirectRuleFixture extends ForgeFixture
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
        ];
    }
}
