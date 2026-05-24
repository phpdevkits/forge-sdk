<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Enums;

/**
 * Comparison operators a monitor applies against its threshold.
 *
 * Mirrors the `MonitorOperator` enum in Forge's OpenAPI spec.
 */
enum MonitorOperator: string
{
    case GreaterThanOrEqual = 'gte';
    case LessThanOrEqual = 'lte';
}
