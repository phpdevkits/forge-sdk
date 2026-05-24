<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Enums;

/**
 * System users a daemon (background process) can run as.
 *
 * Mirrors the `user` enum on Forge's CreateBackgroundProcessRequest.
 */
enum DaemonUser: string
{
    case Root = 'root';
    case Forge = 'forge';
}
