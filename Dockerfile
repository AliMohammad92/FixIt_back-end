# Use official PHP with Apache
FROM php:8.3-apache

# Install dependencies
RUN apt-get update && apt-get install -y \
    git zip unzip libpq-dev libonig-dev libzip-dev \
    && docker-php-ext-install pdo pdo_pgsql mbstring zip exif pcntl bcmath

# Enable mod_rewrite
RUN a2enmod rewrite

# Copy project files
COPY . /var/www/html

# Set working directory
WORKDIR /var/www/html

# Install Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# Run migrations and seeders automatically
RUN php artisan migrate --force \
    && php artisan db:seed --force \
    && php artisan storage:link \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# **Fix Apache DocumentRoot**
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]