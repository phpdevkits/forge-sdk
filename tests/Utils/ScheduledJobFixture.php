<?php

declare(strict_types=1);

namespace Tests\Utils;

use Override;

/**
 * Saloon fixture for the scheduled-job endpoints. Inherits the shared
 * redactions in {@see ForgeFixture}, scrubs the captured command `output`
 * (raw stdout/stderr of the job's last run), and stabilizes pagination
 * cursors.
 */
final class ScheduledJobFixture extends ForgeFixture
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
            'output' => 'REDACTED',
        ];
    }
}
