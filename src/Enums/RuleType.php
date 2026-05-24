<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Enums;

/**
 * The type of a firewall rule.
 *
 * Mirrors the `RuleType` enum in Forge's OpenAPI spec.
 */
enum RuleType: string
{
    case Allow = 'allow';
    case Deny = 'deny';
}
