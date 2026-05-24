<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Enums;

/**
 * Metrics a server monitor can watch.
 *
 * Mirrors the `MonitorMetricType` enum in Forge's OpenAPI spec.
 */
enum MonitorMetricType: string
{
    case CpuLoad = 'cpu_load';
    case Disk = 'disk';
    case FreeMemory = 'free_memory';
    case UsedMemory = 'used_memory';
}
