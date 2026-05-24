<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Enums;

use PhpDevKits\ForgeSdk\Data\CreateSiteData;

/**
 * Application types Forge can scaffold a site for.
 *
 * Mirrors the `SiteType` enum in Forge's OpenAPI spec. Used as the required
 * `type` field on {@see CreateSiteData}.
 */
enum SiteType: string
{
    case Laravel = 'laravel';
    case Symfony = 'symfony';
    case Statamic = 'statamic';
    case WordPress = 'wordpress';
    case PhpMyAdmin = 'phpmyadmin';
    case Php = 'php';
    case NextJs = 'nextjs';
    case NuxtJs = 'nuxtjs';
    case StaticHtml = 'static-html';
    case Other = 'other';
    case Custom = 'custom';
}
