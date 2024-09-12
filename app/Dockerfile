# PHP 8.3 FPM with Composer and Symfony support
FROM php:8.3-fpm

# Install required system dependencies
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libpq-dev \
    libzip-dev \
    unzip \
    git \
    curl \
    libonig-dev \
    libxml2-dev \
    libmemcached-dev \
    zlib1g-dev

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql intl zip opcache

# Install Memcached extension
RUN pecl install memcached && docker-php-ext-enable memcached

# Install Composer globally
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory to /var/www/symfony
WORKDIR /app

# Copy existing application code to /var/www/symfony
COPY . /app

# Install Symfony CLI
RUN curl -sS https://get.symfony.com/cli/installer | bash
RUN mv /root/.symfony*/bin/symfony /usr/local/bin/symfony

# Install Symfony dependencies
RUN composer install --no-scripts --no-autoloader

# Set appropriate file permissions for Symfony
RUN chown -R www-data:www-data /app

# Expose port 9000 for PHP-FPM
EXPOSE 9000

# Start PHP-FPM server
CMD ["php-fpm"]