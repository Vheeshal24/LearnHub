#!/usr/bin/env bash
echo "Running deployment script..."

# Navigate to project root
cd "$(dirname "$0")/.."

# Run migrations
echo "Wiping database and running fresh migrations..."
php artisan migrate:fresh --force

# Run specific Seeder for dummy data
# Run Seeder with UNLIMITED memory
echo "Seeding database..."
php -d memory_limit=-1 artisan db:seed --class=DemoContentSeeder --force

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
