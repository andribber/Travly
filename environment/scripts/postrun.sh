#!/bin/bash
set -e

# Wait for the database to be ready
/usr/local/bin/wait-for-it.sh db:3306 --timeout=30 --strict -- echo "Database is up"

echo "setting up Laravel..."
chown -R www-data:www-data /var/www/storage

# Check if .env exists, if not, copy from .env.example
if [ ! -f /var/www/.env ]; then
  cp /var/www/.env.example /var/www/.env
  echo ".env copied from .env.example"
fi

echo "Setting up composer..."
composer install

echo "Running command to configure project..."
php artisan project:setup

# Execute the main process
exec "$@"
