# 1. Base image with PHP 8.2 + Composer + Node
FROM php:8.2-fpm

# 2. Set working directory
WORKDIR /var/www/html

# 3. Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    curl \
    npm \
    && docker-php-ext-install pdo_mysql mbstring zip gd bcmath \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# 4. Copy project files
COPY . .

# 5. Install PHP dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# 6. Install Node dependencies and build frontend
RUN npm install
RUN npm run production

# 7. Laravel optimizations
RUN php artisan optimize
RUN php artisan config:cache
RUN php artisan route:cache

# 8. Expose port 9000 for PHP-FPM
EXPOSE 9000

# 9. Start PHP-FPM server
CMD ["php-fpm"]
