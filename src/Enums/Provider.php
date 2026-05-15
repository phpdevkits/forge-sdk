<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Enums;

/**
 * Forge's known compute provider slugs.
 *
 * Forge's OpenAPI does not declare `Provider.slug` as a closed enum, so
 * `Data\Provider->slug` stays a plain string and the SDK keeps hydrating
 * responses even when Forge adds a new provider. These cases exist as a
 * convenience for client code that wants a symbolic name — e.g.
 * `$provider->slug === Provider::DigitalOcean->value`.
 */
enum Provider: string
{
    case DigitalOcean = 'digitalocean';
    case Linode = 'linode';
    case Akamai = 'akamai';
    case Vultr = 'vultr';
    case Aws = 'aws';
    case Hetzner = 'hetzner';
    case Custom = 'custom';
}
