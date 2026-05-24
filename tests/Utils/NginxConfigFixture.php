<?php

declare(strict_types=1);

namespace Tests\Utils;

use Override;

/**
 * Saloon fixture for the site Nginx-config endpoint. The raw config exposes
 * the site's real server names and filesystem paths, so the whole `content`
 * blob is overwritten with a neutral placeholder on top of the shared
 * {@see ForgeFixture} redactions.
 */
final class NginxConfigFixture extends ForgeFixture
{
    /**
     * @return array<string, string|int|callable>
     */
    #[Override]
    protected function defineSensitiveJsonParameters(): array
    {
        return [
            ...parent::defineSensitiveJsonParameters(),
            'content' => "server {\n    listen 80;\n    server_name example-site.on-forge.com;\n    root /home/forge/example-site/public;\n}\n",
        ];
    }
}
