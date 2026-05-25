<?php

declare(strict_types=1);

namespace Tests\Utils;

use Override;

/**
 * Saloon fixture for the site log endpoints. A log body is a single opaque
 * string that leaks request IPs, filesystem paths, and potentially secrets,
 * so the whole `content` blob is overwritten with a neutral placeholder on
 * top of the shared {@see ForgeFixture} redactions.
 */
final class SiteLogFixture extends ForgeFixture
{
    /**
     * @return array<string, string|int|callable>
     */
    #[Override]
    protected function defineSensitiveJsonParameters(): array
    {
        return [
            ...parent::defineSensitiveJsonParameters(),
            'content' => "203.0.113.10 - - [01/Jan/2026:00:00:00 +0000] \"GET / HTTP/1.1\" 200 1024\n",
        ];
    }
}
