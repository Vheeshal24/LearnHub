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

# --- ADDED FOR POSTGRES ---
# Install postgres client libraries so PHP can communicate with Render's DB
RUN apk add --no-cache postgresql-dev \
    && docker-php-ext-install pdo_pgsql
# --------------------------

# Copy app files
COPY . .

# Copy built frontend from Stage 1
COPY --from=frontend /app/public/build ./public/build

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Render Config
ENV WEBROOT /var/www/html/public
ENV APP_ENV production
ENV RUN_SCRIPTS 1

# Permissions
RUN chmod -R 775 storage bootstrap/cache

CMD ["/start.sh"]