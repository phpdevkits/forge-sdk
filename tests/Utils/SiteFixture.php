<?php

declare(strict_types=1);

namespace Tests\Utils;

use Override;

/**
 * Saloon fixture for the `/servers/{server}/sites` endpoints. Inherits the
 * shared redactions in {@see ForgeFixture} and adds site-specific ones —
 * notably `deployment_url`, which embeds a secret deploy token, and the
 * site / repository URLs which carry the recorder's real domains.
 */
final class SiteFixture extends ForgeFixture
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
            // `url` is a leaf key on both the site and its repository sub-object.
            'url' => 'https://example-site.test',
            'deployment_url' => 'REDACTED',     // contains a deploy token
            'healthcheck_url' => 'REDACTED',
            'next_cursor' => $stableCursor,
            'prev_cursor' => $stableCursor,
        ];
    }
}
