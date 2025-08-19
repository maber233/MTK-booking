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


# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Install PHP dependencies
RUN composer install --ignore-platform-reqs --no-dev --optimize-autoloader

# Rename config files and set up environment
RUN mv config/init.php.dist config/init.php \
    && mv config/autoload/local.php.dist config/autoload/local.php \
    && mv public/.htaccess_original public/.htaccess || mv public/.htaccess_alternative public/.htaccess

# Set permissions for writable directories
RUN chmod -R 777 data/cache/ data/log/ data/session/ public/docs-client/upload/ public/imgs-client/upload/

# Remove setup tool and clear cache after setup
RUN rm -f public/setup.php \
    && rm -rf data/cache/*

# Set permissions
RUN chown -R www-data:www-data /var/www/html

# Expose port 80
EXPOSE 80

# Health check endpoint
HEALTHCHECK --interval=30s --timeout=5s CMD curl -f http://localhost/ || exit 1
