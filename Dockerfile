# Use the official PHP-FPM image as the base image
FROM php:8.2-fpm

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    nginx \
    libzip-dev \
    unzip \
    git \
    && docker-php-ext-install zip

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set the working directory
WORKDIR /var/www/html

# Install PHPMailer via Composer
RUN composer require phpmailer/phpmailer

# Copy Nginx configuration file
COPY nginx.conf /etc/nginx/nginx.conf

# Copy the PHP script or application files to the container
COPY . /var/www/html

# Expose ports for Nginx
EXPOSE 80

# Start Nginx and PHP-FPM
CMD ["sh", "-c", "php-fpm & nginx -g 'daemon off;'"]
