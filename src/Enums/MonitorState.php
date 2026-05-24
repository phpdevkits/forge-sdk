<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Enums;

/**
 * Alert states a monitor can report.
 *
 * Mirrors the `MonitorState` enum in Forge's OpenAPI spec. `Data\Monitor->state`
 * stays a plain string for forward-compatibility; these cases are a convenience
 * for client code (e.g. `$monitor->state === MonitorState::Alert->value`).
 */
enum MonitorState: string
{
    case Ok = 'OK';
    case Alert = 'ALERT';
    case Unknown = 'UNKNOWN';
}
