<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Enums;

/**
 * Actions that can be triggered on a server via POST /servers/{id}/actions.
 *
 * Mirrors the `ServerAction` enum in Forge's OpenAPI spec.
 */
enum ServerAction: string
{
    case Reboot = 'reboot';
    case PowerCycle = 'power-cycle';
}
