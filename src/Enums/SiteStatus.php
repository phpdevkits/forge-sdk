<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Enums;

/**
 * Lifecycle states a site can be in.
 *
 * Mirrors the `SiteStatus` enum in Forge's OpenAPI spec. `Data\Site->status`
 * stays a plain string for forward-compatibility; these cases are a
 * convenience for client code (e.g. `$site->status === SiteStatus::Deployed->value`).
 */
enum SiteStatus: string
{
    case Installed = 'installed';
    case Creating = 'creating';
    case Removing = 'removing';
    case Installing = 'installing';
    case Uninstalling = 'uninstalling';
    case Deployed = 'deployed';
    case NeverDeployed = 'never-deployed';
    case Deploying = 'deploying';
    case Failed = 'failed';
    case Maintenance = 'maintenance';
}
