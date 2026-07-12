#!/usr/bin/env bash

set -Eeuo pipefail

# Run the Docker Engine provided by the default WSL distribution while keeping
# Git Bash's current directory and bind-mount paths meaningful inside WSL.
case "$PWD" in
    /[[:alpha:]]/*)
        drive="${PWD:1:1}"
        wsl_cwd="/mnt/${drive,,}/${PWD:3}"
        ;;
    *)
        printf 'ERROR: Cannot map Git Bash directory to WSL: %s\n' "$PWD" >&2
        exit 1
        ;;
esac

converted_args=()
for argument in "$@"; do
    case "$argument" in
        /[[:alpha:]]/*)
            drive="${argument:1:1}"
            converted_args+=("/mnt/${drive,,}/${argument:3}")
            ;;
        [[:alpha:]]:[/\\]*)
            drive="${argument:0:1}"
            path_part="${argument:2}"
            path_part="$(printf '%s' "$path_part" | tr '\\' '/')"
            converted_args+=("/mnt/${drive,,}${path_part}")
            ;;
        *)
            converted_args+=("$argument")
            ;;
    esac
done

wsl_options=()
if [ -n "${DOCKER_WSL_DISTRO:-}" ]; then
    wsl_options=(-d "$DOCKER_WSL_DISTRO")
fi

# A detached container does not keep this WSL installation alive by itself.
# Keep one inert Linux process running so `docker compose up -d` remains up
# after wsl.exe returns control to Git Bash.
if [ "${DOCKER_WSL_KEEPALIVE:-true}" = "true" ]; then
    MSYS_NO_PATHCONV=1 wsl.exe "${wsl_options[@]}" -- sh -c \
        'pgrep -f "^sleep infinity$" >/dev/null 2>&1 || setsid -f sleep infinity'
fi

MSYS_NO_PATHCONV=1 exec wsl.exe "${wsl_options[@]}" \
    --cd "$wsl_cwd" -- docker "${converted_args[@]}"
