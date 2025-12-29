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

# Permissions for Laravel folders
RUN chmod -R 775 storage bootstrap/cache

# Increase PHP memory limit for large seeders
RUN echo "memory_limit=512M" > /usr/local/etc/php/conf.d/memory-limit.ini

CMD ["/start.sh"]