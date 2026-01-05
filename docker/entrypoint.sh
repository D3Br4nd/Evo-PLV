#!/usr/bin/env sh
set -eu

# Clear Laravel caches on container start.
# This avoids stale route/config/view caches when /app/storage is mounted as a persistent volume.
#
# Disable by setting:
#   LARAVEL_CLEAR_CACHE_ON_START=false
if [ "${LARAVEL_CLEAR_CACHE_ON_START:-true}" = "true" ]; then
  if [ -f /app/artisan ]; then
    echo "[entrypoint] Clearing Laravel cache (optimize:clear)..."
    # Don't block startup if cache clear fails for any reason.
    php /app/artisan optimize:clear --no-ansi || true
  fi
fi

# Hand off to the original FrankenPHP/Laravel entrypoint.
exec docker-php-entrypoint "$@"


