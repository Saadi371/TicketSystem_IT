# Use the official PHP image as the base image
FROM php:8.2-cli

# Set environment variables for non-interactive installations
ENV DEBIAN_FRONTEND=noninteractive

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    git \
    && docker-php-ext-install zip

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set the working directory
WORKDIR /home/saad/Documents/ticketsystem/TicketSystem_IT

# Install PHPMailer via Composer
RUN composer require phpmailer/phpmailer

# Copy the PHP script or application files to the container
# COPY . /var/www/html

# Expose port 80 for the web server (optional)
 EXPOSE 5000

# Command to run when the container starts (optional, adjust as needed)
# CMD ["php", "your-script.php"]
