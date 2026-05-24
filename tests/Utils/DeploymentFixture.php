<?php

declare(strict_types=1);

namespace Tests\Utils;

use Override;

/**
 * Saloon fixture for the deployments endpoints. Inherits the shared
 * redactions in {@see ForgeFixture} and redacts the deployment-script
 * `content` (which embeds the real site path) plus pagination cursors.
 */
final class DeploymentFixture extends ForgeFixture
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
            'content' => "cd /home/forge/example.com\ngit pull origin main",
            // commit.author is the real git author name from the recorder's repo.
            'author' => 'Test Author',
            'next_cursor' => $stableCursor,
            'prev_cursor' => $stableCursor,
        ];
    }
}
