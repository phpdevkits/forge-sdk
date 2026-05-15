<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Enums;

/**
 * Forge's server-role classifications.
 *
 * Mirrors the `ServerType` enum in Forge's OpenAPI spec. `Data\Server->type`
 * stays a plain string for forward-compatibility (consistent with how the
 * SDK treats provider/disk/architecture); these cases are a convenience
 * for client code — e.g. `$server->type === ServerType::Web->value`.
 */
enum ServerType: string
{
    case App = 'app';
    case Web = 'web';
    case LoadBalancer = 'loadbalancer';
    case Database = 'database';
    case Cache = 'cache';
    case Worker = 'worker';
    case Meilisearch = 'meilisearch';
    case OpenClaw = 'openclaw';
}
