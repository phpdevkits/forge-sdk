<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Enums;

/**
 * Ubuntu releases Forge supports for new servers.
 *
 * Inline enum on `CreateServerRequest.ubuntu_version` in Forge's OpenAPI spec.
 */
enum UbuntuVersion: string
{
    case Ubuntu2204 = '22.04';
    case Ubuntu2404 = '24.04';
}
