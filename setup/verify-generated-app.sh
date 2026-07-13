#!/usr/bin/env bash

set -Eeuo pipefail

readonly APP_DIR="${1:-.}"
readonly SCRIPT_DIR="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd)"
readonly MANIFEST="$SCRIPT_DIR/../templates/app.sha256"

if [ ! -f "$APP_DIR/artisan" ] || [ ! -f "$MANIFEST" ]; then
    printf 'ERROR: application or reproducibility manifest not found.\n' >&2
    exit 1
fi

temporary_manifest="$(mktemp)"
trap 'rm -f "$temporary_manifest"' EXIT

cd "$APP_DIR"

# Secrets, databases, dependency directories and framework caches are runtime
# state. Lockfiles and compiled public assets remain in the byte-level check.
find . \
    -type f \
    ! -path './.git/*' \
    ! -path './bootstrap/cache/*' \
    ! -path './node_modules/*' \
    ! -path './storage/*' \
    ! -path './vendor/*' \
    ! -path './.env' \
    ! -path './.phpunit.result.cache' \
    ! -path './database/database.sqlite' \
    ! -path './public/hot' \
    ! -path './testing' \
    -print0 \
    | LC_ALL=C sort -z \
    | xargs -0 sha256sum > "$temporary_manifest"

if ! diff --unified "$MANIFEST" "$temporary_manifest"; then
    printf 'ERROR: the generated application is not byte-for-byte identical.\n' >&2
    exit 1
fi

printf 'OK: generated application matches %s byte for byte.\n' "$MANIFEST"
