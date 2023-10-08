# Use the official PHP 8 image as the base image
FROM php:8.1-fpm

# Set the working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip xdebug

RUN { \
        echo "xdebug.mode=debug"; \
        echo "xdebug.start_with_request=yes"; \
        echo "xdebug.client_host=host.docker.internal"; \
        echo "xdebug.client_port=9000"; \
    } > /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \

# Install Composer (a PHP package manager)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Mount a local folder to /var/www/html
VOLUME ./:/var/www/html

# Install project dependencies
#RUN composer install --optimize-autoloader --dev

# Set the appropriate file permissions
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html

# Expose port 9000 for PHP-FPM
EXPOSE 9000

# Run the PHP-FPM server
CMD ["php-fpm"]
