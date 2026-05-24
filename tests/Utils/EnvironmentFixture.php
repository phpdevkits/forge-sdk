<?php

declare(strict_types=1);

namespace Tests\Utils;

use Override;

/**
 * Saloon fixture for the site environment-file endpoint. The `.env` body is
 * entirely secret, so the whole `content` blob is overwritten with a
 * fabricated placeholder (never scrubbed line-by-line) on top of the shared
 * {@see ForgeFixture} redactions.
 */
final class EnvironmentFixture extends ForgeFixture
{
    /**
     * @return array<string, string|int|callable>
     */
    #[Override]
    protected function defineSensitiveJsonParameters(): array
    {
        return [
            ...parent::defineSensitiveJsonParameters(),
            'content' => "APP_NAME=Laravel\nAPP_ENV=production\nAPP_KEY=base64:REDACTED\nAPP_DEBUG=false\nDB_CONNECTION=mysql\nDB_PASSWORD=REDACTED\n",
        ];
    }
}
