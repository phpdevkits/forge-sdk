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
 * name anywhere in the body (not by dot-path). The regex pass covers slugs
 * embedded in URL paths (`.../orgs/<slug>`), with its own sequential mapper
 * that walks the body in the same order as the JSON-key pass — so the
 * placeholder for a given real slug is consistent across both contexts.
 */
class ForgeFixture extends Fixture
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
            'slug' => $this->sequentialPlaceholder('test-org-'),
        ];
    }

    /**
     * Regex pass for slugs embedded in URL paths (e.g.
     * `https://forge.laravel.com/api/orgs/<slug>`). The JSON-key pass above
     * has already replaced bare `slug` fields with placeholders; this pass
     * brings URL paths into line so a reader of the fixture can't read off
     * the real slug from `links.self.href`.
     *
     * @return array<string, string|callable>
     */
    #[Override]
    protected function defineSensitiveRegexPatterns(): array
    {
        $mapper = $this->sequentialPlaceholder('test-org-');

        // Matches `/orgs/<slug>` whether the slashes are raw (`/`) or
        // JSON-escaped (`\/`), since json_encode escapes forward slashes by
        // default in the body string at this stage.
        return [
            '#\\\\?/orgs\\\\?/[a-z0-9_-]+#i' => static function (string $match) use ($mapper): string {
                $slashPos = strrpos($match, '/');
                $slug = substr($match, $slashPos + 1);
                $prefix = substr($match, 0, $slashPos + 1);

                return $prefix.$mapper($slug);
            },
        ];
    }

    /**
     * Stable sequential anonymizer: distinct inputs get distinct outputs
     * (`{prefix}1`, `{prefix}2`, ...), and the same input always maps to the
     * same placeholder within a single record session.
     */
    private function sequentialPlaceholder(string $prefix): callable
    {
        $map = [];

        return static function (mixed $value) use ($prefix, &$map): string {
            if (! is_string($value)) {
                return $prefix.'unknown';
            }

            if (! isset($map[$value])) {
                $map[$value] = $prefix.(count($map) + 1);
            }

            return $map[$value];
        };
    }
}
