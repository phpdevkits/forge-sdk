<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Enums;

/**
 * The Nginx log files exposed per-site under
 * GET|DELETE /sites/{site}/logs/{log}.
 *
 * Mirrors the fixed `nginx-access` / `nginx-error` path segments in Forge's
 * OpenAPI spec (NginxAccessLogResource / NginxErrorLogResource).
 */
enum SiteLog: string
{
    case NginxAccess = 'nginx-access';
    case NginxError = 'nginx-error';
}
