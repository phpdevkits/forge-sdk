<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Enums;

/**
 * Lifecycle states of a deployment.
 *
 * Mirrors the `DeploymentStatus` enum in Forge's OpenAPI spec.
 * `Data\Deployment->status` stays a plain string for forward-compatibility;
 * these cases are a convenience for client code.
 */
enum DeploymentStatus: string
{
    case Cancelled = 'cancelled';
    case Deploying = 'deploying';
    case Failed = 'failed';
    case FailedBuild = 'failed-build';
    case Finished = 'finished';
    case Pending = 'pending';
    case Queued = 'queued';
}
