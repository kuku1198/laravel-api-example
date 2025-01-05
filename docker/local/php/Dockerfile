FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Xdebug
#RUN pecl install xdebug \
#    && docker-php-ext-enable xdebug

# Composer Install
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Set user to www-data
RUN chown -R www-data:www-data /var/www/html
USER www-data

# Laravel
#RUN php artisan config:cache && php artisan route:cache


# 기본 명령
EXPOSE 9000
CMD ["php-fpm"]
