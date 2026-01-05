#
# Production image (no Vite dev server). Builds PHP deps and frontend assets at build time.
#

FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
# We run without scripts here because Laravel's Composer scripts need the full app (artisan) present.
# We'll run `php artisan package:discover` later in the final image after copying the app.
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress --optimize-autoloader --no-scripts

FROM node:24-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json vite.config.js ./
COPY resources ./resources
COPY public ./public
RUN npm ci --no-fund --no-audit
RUN npm run build

FROM dunglas/frankenphp:php8.4 AS app

RUN install-php-extensions \
    pdo_pgsql \
    redis \
    bcmath \
    intl \
    zip \
    pcntl

WORKDIR /app

# Entrypoint wrapper: clears Laravel caches on start, then calls original docker-php-entrypoint.
COPY docker/entrypoint.sh /usr/local/bin/plv-entrypoint
RUN chmod +x /usr/local/bin/plv-entrypoint

# App source
COPY . /app

# Replace vendor + built assets with production outputs
COPY --from=vendor /app/vendor /app/vendor
COPY --from=assets /app/public/build /app/public/build

# Ensure public storage symlink exists in the container image
RUN rm -rf /app/public/storage && ln -s /app/storage/app/public /app/public/storage

# Caddy/FrankenPHP config (without FrankenPHP worker mode to avoid crashes)
COPY Caddyfile /etc/frankenphp/Caddyfile

# Basic permissions (storage should be mounted as a volume in prod)
RUN mkdir -p /app/storage /app/bootstrap/cache && chmod -R 775 /app/storage /app/bootstrap/cache

# Generate Laravel package manifest (needed when composer scripts are skipped).
RUN php artisan package:discover --ansi

ENTRYPOINT ["plv-entrypoint"]
CMD ["--config","/etc/frankenphp/Caddyfile","--adapter","caddyfile"]
