<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Enums;

/**
 * PHP runtimes Forge installs on a server.
 *
 * Mirrors the `PhpVersion` enum in Forge's OpenAPI spec.
 */
enum PhpVersion: string
{
    case Php5 = 'php5';
    case Php56Old = 'php56-old';
    case Php56 = 'php56';
    case Php70 = 'php70';
    case Php71 = 'php71';
    case Php72 = 'php72';
    case Php73 = 'php73';
    case Php74 = 'php74';
    case Php80 = 'php80';
    case Php81 = 'php81';
    case Php82 = 'php82';
    case Php83 = 'php83';
    case Php84 = 'php84';
    case Php85 = 'php85';
}
