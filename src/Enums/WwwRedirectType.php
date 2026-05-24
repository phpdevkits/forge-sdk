<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Enums;

/**
 * `www` redirection modes applied to a site's domain.
 *
 * Mirrors the `WwwRedirectType` enum in Forge's OpenAPI spec.
 */
enum WwwRedirectType: string
{
    case FromWww = 'from-www';
    case ToWww = 'to-www';
    case None = 'none';
}
