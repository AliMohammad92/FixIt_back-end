#!/usr/bin/env bash
# scripts/00-laravel-deploy.sh

set -e # Exit immediately if a command exits with a non-zero status.

echo "Starting Laravel deployment tasks..."

# 1. Run Database Migrations
echo "Running database migrations..."
# The --force flag is required in a production environment
php /app/artisan migrate --force

# Check if migrations were successful before proceeding
if [ $? -ne 0 ]; then
    echo "ERROR: Database migrations failed. Aborting deployment script."
    exit 1
fi

# 2. Run Database Seeders
# This assumes you want to run the default DatabaseSeeder or all seeders.
echo "Running database seeders..."
# Use --class=SpecificSeeder to run only one, or leave it blank for all.
php /app/artisan db:seed --force

# Check if seeding was successful before concluding
if [ $? -ne 0 ]; then
    echo "WARNING: Database seeding failed. The app might be missing initial data."
    # We exit with 0 (success) here, as sometimes the app can still run,
    # but a failing seeder in production is often a problem. You can change this to 'exit 1' if it's critical.
    exit 0
fi

echo "Deployment tasks complete. Database is up-to-date and seeded."
exit 0