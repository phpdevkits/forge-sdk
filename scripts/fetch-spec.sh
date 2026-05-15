#!/usr/bin/env bash
#
# Refresh docs/forge.openapi.json from the upstream Forge API spec.
#
# Run from the repository root:
#
#     ./scripts/fetch-spec.sh
#
# Then review the diff before committing — Forge ships breaking changes through
# this spec, so each refresh deserves a manual look.

set -euo pipefail

SPEC_URL="https://forge.laravel.com/api/docs.openapi"
TARGET="docs/forge.openapi.json"

if [[ ! -d "$(dirname "$TARGET")" ]]; then
    echo "Run this script from the repository root." >&2
    exit 1
fi

echo "Fetching $SPEC_URL"
curl --fail --silent --show-error --location "$SPEC_URL" --output "$TARGET"

echo "Wrote $TARGET ($(wc -c < "$TARGET") bytes)"
