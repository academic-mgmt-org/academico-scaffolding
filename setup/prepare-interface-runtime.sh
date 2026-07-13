#!/usr/bin/env bash

set -Eeuo pipefail

readonly APP_DIR="${1:-.}"
readonly SAIL_BASE_IMAGE='ariaieboy/sail-runtime-image:8.5-24@sha256:d9f7f1ee244847612252222265d71e2340417a812a15d1cfa9f3433dafb5ea75'

for command_name in docker grep php sed; do
    if ! command -v "$command_name" >/dev/null 2>&1; then
        printf 'ERROR: falta el comando requerido: %s\n' "$command_name" >&2
        exit 1
    fi
done

if [[ ! -f "$APP_DIR/artisan" ]]; then
    printf 'ERROR: no se encontró una aplicación Laravel en %s.\n' "$APP_DIR" >&2
    exit 1
fi

cd "$APP_DIR"

if [[ ! -f compose.yaml ]]; then
    DOCKER_HOST=unix:///dev/null \
        php artisan sail:install --with=none --no-interaction
fi

grep -q 'WEBSERVER: cli' compose.yaml || \
    sed -i "/WWWUSER:/i\            WWWGROUP: '\${WWWGROUP}'\n            WEBSERVER: cli" compose.yaml

sed -i \
    -e "s/'\${WWWUSER}'/'\${WWWUSER:-1000}'/g" \
    -e "s/'\${WWWGROUP}'/'\${WWWGROUP:-1000}'/g" \
    compose.yaml

printf '%s\n' \
    "FROM $SAIL_BASE_IMAGE" \
    'RUN apt-get update \' \
    '    && apt-get install -y --no-install-recommends php8.5-grpc \' \
    '    && apt-get clean \' \
    '    && rm -rf /var/lib/apt/lists/*' | \
    docker build --pull \
        --tag sail-8.5/app \
        --file - \
        .

docker compose config >/dev/null
docker compose up -d --no-build
docker compose exec -T laravel.test php --ri grpc >/dev/null
docker compose exec -T laravel.test php artisan migrate --force
docker compose exec -T laravel.test npm ci
docker compose exec -T laravel.test npm run build

printf 'OK: runtime Sail con gRPC disponible en http://localhost:%s\n' "${APP_PORT:-8000}"
