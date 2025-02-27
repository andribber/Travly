#!/bin/bash
set -e

# Wait for the database to be ready
/usr/local/bin/wait-for-it.sh db:3306 --timeout=30 --strict -- echo "Database is up"

# Check if the initialization flag file exists
if [ ! -f /var/www/.initialized ]; then
  echo "First-time initialization: setting up Laravel..."
  echo "Copying env.example file..."
  cp .env.example .env
  echo "Setting up all the project dependencies"
  composer install
  echo "Setting up the project key"
  php artisan key:generate
  echo "Setting up the JWT Secret"
  php artisan jwt:secret
  echo "Running the migrations and seed the database"
  php artisan migrate --seed
  chown -R www-data:www-data /var/www/storage

  # Create a flag file to avoid re-running the setup
  touch /var/www/.initialized
else
  echo "Container already initialized, skipping one-time setup."
fi

# Execute the main process
exec "$@"
