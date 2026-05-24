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
     * Regex pass for identifiers embedded in URL paths (`links.self.href`,
     * `meta.path`) and host names — the JSON-key pass only scrubs bare leaf
     * fields, so without this a reader could recover the real org slug, the
     * account's numeric resource ids, or a site's on-forge hostname straight
     * from a URL string.
     *
     * Patterns match whether slashes are raw (`/`) or JSON-escaped (`\/`),
     * since `json_encode` escapes forward slashes in the stored body.
     *
     * @return array<string, string|callable>
     */
    #[Override]
    protected function defineSensitiveRegexPatterns(): array
    {
        $mapper = $this->sequentialPlaceholder('test-org-');

        return [
            // `/orgs/<slug>` → sequential `test-org-N` placeholder.
            '#\\\\?/orgs\\\\?/[a-z0-9_-]+#i' => static function (string $match) use ($mapper): string {
                $slashPos = strrpos($match, '/');

                return substr($match, 0, $slashPos + 1).$mapper(substr($match, $slashPos + 1));
            },
            // Account-specific numeric resource ids in any nested URL segment
            // (`/servers/123`, `/sites/123`, `/background-processes/123`, ...)
            // collapse to `1`, matching the redacted `id` leaf.
            '#\\\\?/(servers|sites|background-processes|deployments|ssh-keys|keys|providers|regions|sizes|credentials|server-credentials|vpcs|schemas|users|backups|instances|restores|monitors|firewall-rules|scheduled-jobs|redirect-rules)\\\\?/\d+#' => static fn (string $match): string => (string) preg_replace('/\d+$/', '1', $match),
            // Site hostnames (`<name>.on-forge.com`, incl. inside `/home/forge/...`
            // directory paths) → a neutral placeholder.
            '#[a-z0-9][a-z0-9.-]*\.on-forge\.com#i' => 'example-site.on-forge.com',
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
