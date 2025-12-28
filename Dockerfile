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

# Copy app files
COPY . .

# Copy built frontend from Stage 1 (Matched the name 'frontend' here)
COPY --from=frontend /app/public/build ./public/build

# Install PHP dependencies via Composer (This image already has composer)
RUN composer install --no-dev --optimize-autoloader

# Render Config
ENV WEBROOT /var/www/html/public
ENV APP_ENV production
ENV RUN_SCRIPTS 1

# Permissions
RUN chmod -R 775 storage bootstrap/cache

# The richarvey image uses /start.sh to launch Nginx and PHP together
CMD ["/start.sh"]