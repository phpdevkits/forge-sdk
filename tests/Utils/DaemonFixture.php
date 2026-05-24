<?php

declare(strict_types=1);

namespace Tests\Utils;

use Override;

/**
 * Saloon fixture for the background-process (daemon) endpoints. Inherits the
 * shared redactions in {@see ForgeFixture} and redacts the daemon log
 * `content` (which can echo arbitrary command output) plus pagination
 * cursors.
 */
final class DaemonFixture extends ForgeFixture
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
            'content' => 'REDACTED',
            'next_cursor' => $stableCursor,
            'prev_cursor' => $stableCursor,
        ];
    }
}
