# Use official PHP image with FPM
FROM php:8.2-fpm

# Set working directory
WORKDIR /var/www

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    curl \
    libonig-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy Laravel files
COPY . .

# Set permissions for Laravel storage
RUN chmod -R 777 storage bootstrap/cache

# Expose PHP port
EXPOSE 9000

# Start PHP-FPM server
CMD ["php-fpm"]
