<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Enums;

use PhpDevKits\ForgeSdk\Data\CreateRedirectRuleData;

/**
 * Redirect-rule modes Forge can apply to a site path.
 *
 * Mirrors the `RedirectRuleType` enum in Forge's OpenAPI spec. Used as the
 * required `type` field on {@see CreateRedirectRuleData}.
 */
enum RedirectType: string
{
    case Redirect = 'redirect';
    case Permanent = 'permanent';
}
