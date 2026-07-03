#!/bin/sh
set -e

echo "==> SilverGro production startup"

# storage/bootstrap must be writable by the php-fpm/nginx worker user
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

# Build framework caches now that runtime env (SSM secrets, DB, S3) is injected.
# Clear first so a redeploy never serves a stale cached config.
php artisan config:clear >/dev/null 2>&1 || true
php artisan config:cache
php artisan route:cache
php artisan view:cache

# NOTE: migrations are intentionally NOT run here. They run as a dedicated
# one-off ECS task (see scripts/deploy.sh) so concurrent tasks never race and
# a bad migration can't crash-loop the web service.

echo "==> Ready. Handing off to: $*"
exec "$@"
