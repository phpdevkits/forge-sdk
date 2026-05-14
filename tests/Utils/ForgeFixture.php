<?php

declare(strict_types=1);

namespace Tests\Utils;

use Override;
use Saloon\Http\Faking\Fixture;

/**
 * Saloon fixture with built-in redaction for forge-sdk recordings.
 *
 * Redaction is applied at record-time (in `Fixture::store()`), so the JSON
 * file on disk is already sanitized — no test-time wrapping needed.
 *
 * Header rules match case-insensitively; JSON key rules match by leaf key
 * name anywhere in the body (not by dot-path).
 */
final class ForgeFixture extends Fixture
{
    /**
     * Headers we strip from every recording — CDN noise plus anything that
     * leaks the recorder's identity, network path, or recording time.
     *
     * @return array<string, string|callable>
     */
    #[Override]
    protected function defineSensitiveHeaders(): array
    {
        return [
            'set-cookie' => 'REDACTED',
            'cf-ray' => 'REDACTED',
            'cf-cache-status' => 'REDACTED',
            'date' => 'REDACTED',
            'server' => 'REDACTED',
            'alt-svc' => 'REDACTED',
            'x-ratelimit-remaining' => 'REDACTED',
            'x-ratelimit-limit' => 'REDACTED',
            'expect-ct' => 'REDACTED',
        ];
    }

    /**
     * Body keys we replace with stable placeholders so fixtures are portable
     * across whoever happens to run the recording.
     *
     * @return array<string, string|callable>
     */
    #[Override]
    protected function defineSensitiveJsonParameters(): array
    {
        return [
            'id' => '1',
            'name' => 'Test User',
            'email' => 'test@example.com',
            'created_at' => '2024-01-01T00:00:00.000000Z',
            'updated_at' => '2024-01-01T00:00:00.000000Z',
        ];
    }
}
