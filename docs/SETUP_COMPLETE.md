# Local Development Database Setup - Complete ✅

## What Was Done

Successfully set up PostgreSQL for local development, replacing the Docker PostgreSQL instance.

## Files Created

1. **`scripts/setup-postgres-dev.sh`** - Automated setup script that:
    - Checks for Homebrew PostgreSQL installation
    - Starts the PostgreSQL service
    - Creates the `dev_user` role with password `devpass`
    - Creates the `silvergro_dev` database
    - Enables required PostgreSQL extensions (uuid-ossp)
    - Updates `.env` with local database credentials
    - Runs `php artisan migrate:fresh --seed --force`

2. **`Makefile`** - Convenient command runner:
    - `make help` - Show available commands
    - `make dev-db-init` - Run the database setup script

3. **`docs/LOCAL_DB_SETUP.md`** - Comprehensive documentation covering:
    - Prerequisites and installation
    - Quick start guide
    - Manual setup instructions
    - Troubleshooting tips
    - Switching between Docker and local PostgreSQL

## Database Configuration

The `.env` file has been updated with:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=silvergro_dev
DB_USERNAME=dev_user
DB_PASSWORD=devpass
```

## Bug Fixes

Fixed a compatibility issue in `database/migrations/2023_06_26_225918_create_permission_tables.php`:

- Replaced deprecated `PermissionRegistrar::$pivotRole` and `PermissionRegistrar::$pivotPermission` static properties
- Now uses local variables `$pivotRole` and `$pivotPermission` compatible with Spatie Laravel Permission v6.20

## Verification

✅ PostgreSQL service running via Homebrew
✅ Database `silvergro_dev` created and owned by `dev_user`
✅ All 73 migrations executed successfully
✅ Seeders completed successfully
✅ Database connection verified
✅ Tables created and accessible

## Usage

### First Time Setup

```bash
# Install PostgreSQL
brew install postgresql@15

# Initialize and seed database
make dev-db-init
```

### Daily Development

PostgreSQL will start automatically on system boot. If you need to manually control it:

```bash
# Start PostgreSQL
brew services start postgresql@15

# Stop PostgreSQL
brew services stop postgresql@15

# Restart PostgreSQL
brew services restart postgresql@15

# Check status
brew services list
```

### Running Migrations

```bash
# Run new migrations
php artisan migrate

# Rollback last migration
php artisan migrate:rollback

# Fresh database with seeders
php artisan migrate:fresh --seed
```

### Connecting to Database

```bash
# Using psql
psql -U dev_user -d silvergro_dev

# List tables
psql -U dev_user -d silvergro_dev -c "\dt"

# Run a query
psql -U dev_user -d silvergro_dev -c "SELECT COUNT(*) FROM users;"
```

## Notes

- The setup script is idempotent - safe to run multiple times
- Database credentials are stored in plain text in `.env` (for development only)
- To change credentials, edit the variables at the top of `scripts/setup-postgres-dev.sh`
- The script uses `migrate:fresh` which **drops all tables** - only use in development!

## Next Steps

You're all set! The application is now configured to use local PostgreSQL for development. You can:

1. Start the Laravel development server: `php artisan serve`
2. Build frontend assets: `npm run dev`
3. Access the application at `http://localhost:8000`

