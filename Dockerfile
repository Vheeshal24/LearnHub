# Stage 1 - Build Frontend (Vite)
FROM node:18-alpine AS frontend
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# Stage 2 - Backend (Laravel + PHP + Nginx)
FROM richarvey/nginx-php-fpm:latest
WORKDIR /var/www/html

# Install postgres client libraries
RUN apk add --no-cache postgresql-dev \
    && docker-php-ext-install pdo_pgsql

# Copy everything first
COPY . .

# --- CRITICAL: Give the script execute permissions ---
RUN chmod +x /var/www/html/scripts/00-laravel-deploy.sh

# Copy built frontend from Stage 1
COPY --from=frontend /app/public/build ./public/build

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Render Config
ENV WEBROOT /var/www/html/public
ENV APP_ENV production
ENV RUN_SCRIPTS 1
# ADD THIS LINE BELOW
ENV ERRORS_PAGES 0
ENV nginx_config_file /var/www/html/conf/nginx/nginx-site.conf

# --- UPDATED PERMISSIONS SECTION ---
# 1. Create the public/storage directory if it doesn't exist (to avoid symlink errors)
RUN mkdir -p /var/www/html/public/storage

# 2. Give the web server (www-data) ownership of the storage and cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public/storage

# 3. Set correct permissions
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

CMD ["/start.sh"]
