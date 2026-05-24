<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Enums;

/**
 * Actions that can be triggered on a daemon via
 * POST /background-processes/{id}/actions.
 *
 * Mirrors the `BackgroundProcessAction` enum in Forge's OpenAPI spec.
 */
enum DaemonAction: string
{
    case Restart = 'restart';
    case Stop = 'stop';
    case Start = 'start';
    case EmptyLog = 'empty-log';
}
