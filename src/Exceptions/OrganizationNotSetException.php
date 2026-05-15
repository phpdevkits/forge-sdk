<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Exceptions;

/**
 * Thrown when an org-scoped accessor (`$forge->servers()` etc.) is called
 * but the `Forge` instance has no organization bound to it — neither via
 * the constructor / config / env, nor via a `$forge->org($slug)` chain.
 *
 * This is a client-side guard, not an HTTP error, so it extends
 * `ForgeException` directly rather than `ApiException`.
 */
final class OrganizationNotSetException extends ForgeException
{
    public static function forAccessor(string $accessor): self
    {
        return new self(sprintf(
            'Forge::%s requires an organization. Set one via the constructor, FORGE_ORGANIZATION, '
            .'forge.json, or chain via $forge->org($slug).',
            $accessor,
        ));
    }
}
