# Local Development Database Setup

This guide explains how to set up PostgreSQL locally for development.

## Prerequisites

1. **Install Homebrew** (if not already installed):
   ```bash
   /bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
   ```

2. **Install PostgreSQL**:
   ```bash
   brew install postgresql@15
   ```

## Quick Start

Run the automated setup script:

```bash
make dev-db-init
```

Or run the script directly:

```bash
./scripts/setup-postgres-dev.sh
```

## What the Script Does

1. **Checks for PostgreSQL** - Ensures Homebrew PostgreSQL is installed
2. **Starts PostgreSQL Service** - Starts the Postgres service via Homebrew
3. **Waits for Availability** - Ensures the database is ready to accept connections
4. **Creates Database Role** - Creates a `dev_user` role with password `devpass`
5. **Creates Database** - Creates `silvergro_dev` database owned by `dev_user`
6. **Enables Extensions** - Enables `uuid-ossp` extension (and others as needed)
7. **Updates .env** - Updates your `.env` file with the local database credentials
8. **Runs Migrations & Seeders** - Executes `php artisan migrate:fresh --seed`

## Configuration

You can modify the database credentials in `scripts/setup-postgres-dev.sh`:

```bash
DB_USER="dev_user"
DB_PASS="devpass"
DB_NAME="silvergro_dev"
DB_HOST="127.0.0.1"
DB_PORT="5432"
```

## Manual Setup (Alternative)

If you prefer to set up manually:

1. Start PostgreSQL:
   ```bash
   brew services start postgresql@15
   ```

2. Create database role:
   ```bash
   psql -c "CREATE ROLE dev_user WITH LOGIN PASSWORD 'devpass';"
   ```

3. Create database:
   ```bash
   createdb -O dev_user silvergro_dev
   ```

4. Update your `.env` file:
   ```
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=silvergro_dev
   DB_USERNAME=dev_user
   DB_PASSWORD=devpass
   ```

5. Run migrations and seeders:
   ```bash
   php artisan migrate:fresh --seed
   ```

## Troubleshooting

### PostgreSQL not found

If `psql` command is not found, ensure PostgreSQL is in your PATH:

```bash
echo 'export PATH="/opt/homebrew/opt/postgresql@15/bin:$PATH"' >> ~/.zshrc
source ~/.zshrc
```

### Connection refused

If you get "connection refused" errors:

1. Check if PostgreSQL is running: `brew services list`
2. Restart the service: `brew services restart postgresql@15`
3. Check logs: `tail -f /opt/homebrew/var/log/postgresql@15.log`

### Permission denied

If you get permission errors, you may need to connect as the default superuser:

```bash
psql postgres
```

## Switching Between Docker and Local

### To use Docker PostgreSQL:

Update `.env`:

```
DB_HOST=127.0.0.1  # or the Docker container name
DB_PORT=5432
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=your_password
```

### To use Local PostgreSQL:

Run the setup script or update `.env` manually to the local credentials.

