FROM php:8.2-cli

# Install system & PHP dependencies
RUN apt-get update && apt-get install -y \
    git unzip libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo_mysql mbstring gd

# Copy Composer dari image resmi
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

# Install dependency composer
RUN composer install --no-dev --optimize-autoloader

# Expose port
EXPOSE 10000

# Jalankan server Laravel di port 10000
CMD php artisan serve --host=0.0.0.0 --port=10000