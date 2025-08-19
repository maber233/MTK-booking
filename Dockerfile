# Use the official PHP image with Apache
FROM php:8.2-apache

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    libzip-dev \
    default-mysql-client \
    libicu-dev \
    && docker-php-ext-install pdo_mysql mysqli intl

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy application code
COPY . /var/www/html/

# Create vendor directory (for autoloader)
RUN mkdir -p /var/www/html/vendor

# Copy config template to actual config file
RUN cp /var/www/html/config/init.php.dist /var/www/html/config/init.php

# Set working directory
WORKDIR /var/www/html

# Set permissions
RUN chown -R www-data:www-data /var/www/html

# Expose port 80
EXPOSE 80

# Health check endpoint
HEALTHCHECK --interval=30s --timeout=5s CMD curl -f http://localhost/ || exit 1
