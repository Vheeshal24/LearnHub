#!/usr/bin/env bash
echo "Running deployment script..."

# Navigate to project root
cd "$(dirname "$0")/.."

# Run migrations
echo "Running migrations..."
php artisan migrate --force

# ... after php artisan migrate --force
echo "Seeding database..."
php artisan db:seed --force

# Clear and cache config
echo "Caching config..."
php artisan config:cache

# Clear and cache routes
echo "Caching routes..."
php artisan route:cache

# Clear and cache views
echo "Caching views..."
php artisan view:cache

echo "Deployment finished."
