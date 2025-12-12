# --- STAGE 1: BUILD ENVIRONMENT ---
FROM php:8.2-fpm-alpine as base

# 1. Install necessary system packages for PHP extensions and tools
RUN apk update && apk add \
    nginx \
    supervisor \
    openssl \
    git \
    curl \
    postgresql-client \
    zip \
    unzip \
    icu-dev \
    libpq \
    && rm -rf /var/cache/apk/*

# 2. Install required PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql intl opcache

# 3. Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory for the application
WORKDIR /app

# 4. Copy application source code
COPY . /app

# 5. Install PHP dependencies using Composer
# We do this here so we can remove the composer cache later
RUN composer install --no-dev --optimize-autoloader

# 6. Generate the Application Key
# This must be run before caching config
# Render will overwrite this value with the ENV variable at runtime.
RUN php artisan key:generate

# 7. Cache configuration and routes for production
RUN php artisan config:cache
RUN php artisan route:cache

# 8. Set correct permissions for storage and bootstrap/cache
RUN chown -R www-data:www-data /app \
    && chmod -R 775 /app/storage \
    && chmod -R 775 /app/bootstrap/cache

# --- STAGE 2: PRODUCTION RUNTIME ---
FROM base

# Copy Nginx config and deploy script
COPY conf/nginx-site.conf /etc/nginx/conf.d/default.conf
COPY scripts/00-laravel-deploy.sh /usr/local/bin/00-laravel-deploy.sh

# Make the deploy script executable
RUN chmod +x /usr/local/bin/00-laravel-deploy.sh

# Expose ports
EXPOSE 8080

# Define the command that runs when the container starts
# We use supervisord to manage both Nginx and PHP-FPM processes
CMD ["supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]