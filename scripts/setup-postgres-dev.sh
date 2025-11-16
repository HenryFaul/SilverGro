#!/usr/bin/env bash
set -euo pipefail

# Config - adjust as needed
DB_USER="dev_user"
DB_PASS="devpass"
DB_NAME="silvergro_dev"
DB_HOST="127.0.0.1"
DB_PORT="5432"
ENV_FILE=".env"

echo "1/6 - Ensure Homebrew Postgres is installed"
if ! command -v psql >/dev/null 2>&1; then
  echo "Postgres not found. Install with: brew install postgresql@15"
  exit 1
fi

echo "2/6 - Start Postgres service (via brew)"
# Try both postgresql@15 and postgresql service names
brew services start postgresql@15 2>/dev/null || brew services start postgresql 2>/dev/null || true

echo "3/6 - Wait for Postgres to become available"
RETRIES=40
COUNT=0
until pg_isready -h "$DB_HOST" -p "$DB_PORT" >/dev/null 2>&1 || [ "$COUNT" -ge "$RETRIES" ]; do
  echo "  Waiting for Postgres... ($COUNT/$RETRIES)"
  sleep 1
  COUNT=$((COUNT+1))
done
if [ "$COUNT" -ge "$RETRIES" ]; then
  echo "Postgres didn't become ready in time"
  echo "Try running: brew services restart postgresql@15"
  exit 1
fi
echo "Postgres is ready!"

echo "4/6 - Create role and database if missing"
# Create role if missing
if ! psql postgres -tAc "SELECT 1 FROM pg_roles WHERE rolname = '$DB_USER'" | grep -q 1; then
  psql postgres -c "CREATE ROLE \"$DB_USER\" WITH LOGIN PASSWORD '$DB_PASS';"
  echo "Created role $DB_USER"
else
  echo "Role $DB_USER already exists"
fi

# Create database if missing
if ! psql postgres -lqt | cut -d \| -f 1 | grep -qw "$DB_NAME"; then
  createdb -O "$DB_USER" "$DB_NAME"
  echo "Created database $DB_NAME owned by $DB_USER"
else
  echo "Database $DB_NAME already exists"
fi

echo "Ensure extensions commonly used by Laravel (adjust as needed)"
psql -d "$DB_NAME" -c "CREATE EXTENSION IF NOT EXISTS \"uuid-ossp\";" >/dev/null 2>&1 || true

echo "5/6 - Update .env DB credentials"
# macOS sed requires an empty suffix for -i
if [ -f "$ENV_FILE" ]; then
  # Update or add DB_CONNECTION
  if grep -q "^DB_CONNECTION=" "$ENV_FILE"; then
    sed -i '' -E "s/^DB_CONNECTION=.*/DB_CONNECTION=pgsql/" "$ENV_FILE"
  else
    # Add DB_CONNECTION before DB_HOST
    sed -i '' '/^DB_HOST=/i\
DB_CONNECTION=pgsql
' "$ENV_FILE"
  fi

  sed -i '' -E "s/^DB_HOST=.*/DB_HOST=$DB_HOST/" "$ENV_FILE"
  sed -i '' -E "s/^DB_PORT=.*/DB_PORT=$DB_PORT/" "$ENV_FILE"
  sed -i '' -E "s/^DB_DATABASE=.*/DB_DATABASE=$DB_NAME/" "$ENV_FILE"
  sed -i '' -E "s/^DB_USERNAME=.*/DB_USERNAME=$DB_USER/" "$ENV_FILE"
  sed -i '' -E "s/^DB_PASSWORD=.*/DB_PASSWORD=$DB_PASS/" "$ENV_FILE"
  echo "Updated $ENV_FILE"
else
  echo "$ENV_FILE not found, create or update it manually."
fi

echo "6/6 - Run migrations and seeders (Laravel)"
# Export DB credentials for this process to be safe
export DB_CONNECTION=pgsql
export DB_HOST="$DB_HOST"
export DB_PORT="$DB_PORT"
export DB_DATABASE="$DB_NAME"
export DB_USERNAME="$DB_USER"
export DB_PASSWORD="$DB_PASS"

# Run migrations and seeders. Use --force for CI / non-interactive.
php artisan migrate:fresh --seed --force

echo "Done. Dev Postgres ready and seeders executed."

