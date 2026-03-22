#!/bin/sh
set -e

echo "==> SilverGro startup"

# Install composer dependencies if vendor is missing
if [ ! -f vendor/autoload.php ]; then
    echo "==> Installing composer dependencies..."
    composer install --no-interaction --no-progress --prefer-dist
fi

# Generate APP_KEY if not set
APP_KEY_VAL=$(grep '^APP_KEY=' .env 2>/dev/null | cut -d= -f2 || true)
if [ -z "$APP_KEY_VAL" ]; then
    echo "==> Generating application key..."
    php artisan key:generate --force
fi

# Fix storage permissions
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Create storage symlink (safe to run multiple times)
php artisan storage:link --force 2>/dev/null || true

# Run migrations
echo "==> Running migrations..."
php artisan migrate --force --no-interaction

echo "==> Ready."
exec "$@"
