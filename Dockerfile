# PHP + Composer
FROM php:8.2-fpm

# System dependencies
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Add Composer
COPY --from=composer:2.8.3 /usr/bin/composer /usr/bin/composer

# Working directory
WORKDIR /var/www

# Copy Laravel files
COPY . .

# Install vendor dependencies
RUN composer install --optimize-autoloader --no-dev



# Set permissions for storage and bootstrap/cache
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Application port
EXPOSE 8000

# Run artisan serve when container starts
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
