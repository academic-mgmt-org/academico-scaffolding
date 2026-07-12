#!/usr/bin/env bash

set -Eeuo pipefail

# Windows compatibility implementation for the TCP/UDP port form used by the
# project. Linux hosts install the full psmisc implementation instead.
if (( $# == 1 )) && [[ "$1" = "--version" || "$1" = "-V" ]]; then
    printf 'fuser (login-scaffolding Windows compatibility) 1.0\n'
    exit 0
fi

kill_requested=false
namespace=""
targets=()

while (( $# > 0 )); do
    case "$1" in
        -k)
            kill_requested=true
            ;;
        -TERM | -KILL | -INT)
            # Windows has no POSIX signals; Stop-Process supplies the closest
            # available termination behavior when -k is requested.
            ;;
        -n | --namespace)
            shift
            (( $# > 0 )) || {
                printf 'ERROR: fuser namespace is missing\n' >&2
                exit 2
            }
            namespace="${1,,}"
            ;;
        --)
            shift
            while (( $# > 0 )); do
                targets+=("$1")
                shift
            done
            break
            ;;
        -*)
            printf 'ERROR: Unsupported fuser option on Windows: %s\n' "$1" >&2
            exit 2
            ;;
        *)
            targets+=("$1")
            ;;
    esac
    shift
done

if (( ${#targets[@]} == 0 )); then
    printf 'Usage: fuser [-k] [-TERM|-KILL|-INT] PORT/tcp\n' >&2
    exit 2
fi

found=false
for target in "${targets[@]}"; do
    if [[ "$target" =~ ^([0-9]+)/(tcp|udp)$ ]]; then
        port="${BASH_REMATCH[1]}"
        protocol="${BASH_REMATCH[2]}"
    elif [[ "$target" =~ ^[0-9]+$ ]] \
        && [[ "$namespace" = "tcp" || "$namespace" = "udp" ]]; then
        port="$target"
        protocol="$namespace"
    else
        printf 'ERROR: Unsupported fuser target on Windows: %s\n' "$target" >&2
        exit 2
    fi

    if FUSER_PORT="$port" \
        FUSER_PROTOCOL="$protocol" \
        FUSER_KILL="$kill_requested" \
        MSYS_NO_PATHCONV=1 powershell.exe -NoLogo -NoProfile -NonInteractive \
            -Command '
$port = [int] $env:FUSER_PORT
$endpoints = if ($env:FUSER_PROTOCOL -eq "tcp") {
    @(Get-NetTCPConnection -LocalPort $port -ErrorAction SilentlyContinue)
} else {
    @(Get-NetUDPEndpoint -LocalPort $port -ErrorAction SilentlyContinue)
}
$ownerProcessIds = @(
    $endpoints |
        Select-Object -ExpandProperty OwningProcess -Unique |
        Where-Object { $_ -gt 0 -and $_ -ne $PID }
)
if ($ownerProcessIds.Count -eq 0) {
    exit 1
}
if ($env:FUSER_KILL -eq "true") {
    foreach ($ownerProcessId in $ownerProcessIds) {
        Stop-Process -Id $ownerProcessId -ErrorAction Stop
    }
}
[Console]::Out.Write(($ownerProcessIds -join " "))
'; then
        printf '\n'
        found=true
    fi
done

$found
