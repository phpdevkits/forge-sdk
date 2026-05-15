<?php

declare(strict_types=1);

namespace Tests\Utils;

use Override;

/**
 * Saloon fixture for the `/orgs/{org}/servers` endpoints. Inherits the
 * shared redactions in {@see ForgeFixture} and adds server-specific
 * leaf-key replacements so recorded fixtures don't leak IPs, SSH public
 * keys, the recorder's timezone, or the real numeric server id.
 */
final class ServerFixture extends ForgeFixture
{
    /**
     * @return array<string, string|int|callable>
     */
    #[Override]
    protected function defineSensitiveJsonParameters(): array
    {
        // Cursors encode created_at timestamps of real servers (base64). Replace
        // non-null cursors with a stable placeholder so fixture-based tests can
        // assert exact cursor values across recordings. Null is preserved so
        // iterators still terminate when an upstream page legitimately has no
        // next page.
        $stableCursor = static fn (mixed $value): ?string => $value === null ? null : 'CURSOR-A';

        return [
            ...parent::defineSensitiveJsonParameters(),
            // RFC 5737 / RFC 1918 reserved ranges — guaranteed-unroutable.
            'ip_address' => '203.0.113.1',
            'private_ip_address' => '10.0.0.1',
            'ssh_port' => 22,
            'timezone' => 'UTC',
            'identifier' => 'test-server-identifier',
            'credential_id' => 1,
            'local_public_key' => 'REDACTED',
            'sudo_password' => 'REDACTED',
            'next_cursor' => $stableCursor,
            'prev_cursor' => $stableCursor,
        ];
    }

    /**
     * Adds a regex pass that anonymizes the numeric server id leaking through
     * `links.self.href` (e.g. `/api/orgs/<slug>/servers/843752` → `.../servers/1`).
     *
     * @return array<string, string|callable>
     */
    #[Override]
    protected function defineSensitiveRegexPatterns(): array
    {
        return [
            ...parent::defineSensitiveRegexPatterns(),
            // Matches `/servers/<numeric-id>` with raw `/` or JSON-escaped `\/`.
            '#\\\\?/servers\\\\?/\d+#' => '/servers/1',
        ];
    }
}
