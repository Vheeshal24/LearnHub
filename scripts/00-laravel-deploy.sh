#!/usr/bin/env bash
echo "Running deployment script..."

# Navigate to project root
cd "$(dirname "$0")/.."

# Run migrations (safe for production)
echo "Running database migrations..."
php artisan migrate --force

# Optional: seed demo data when explicitly enabled
if [ "${SEED_DEMO:-0}" = "1" ]; then
  echo "Seeding demo database..."
  php artisan db:seed --class=DemoContentSeeder --force
fi

# Force the creation of the link even if it exists
echo "Creating storage link..."
php artisan storage:link --force

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
