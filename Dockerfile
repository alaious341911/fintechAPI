# Use the official PHP image with Apache
FROM php:8.2-apache

# Set working directory
WORKDIR /var/www/html

# Install dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    unzip \
    git \
    curl \
    libpq-dev \ 
    && docker-php-ext-install pdo_pgsql \ 
    && apt-get clean && rm -rf /var/lib/apt/lists/* # Clean up

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy Apache configuration
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf


# Copy application files to container
COPY . .

# Install Laravel dependencies
RUN composer install --optimize-autoloader --no-dev

# Set up Laravel application
RUN cp .env.example .env
RUN php artisan key:generate

# Set up Apache
RUN chown -R www-data:www-data /var/www/html && \
    a2enmod rewrite

# Expose port 80 to the Render platform
EXPOSE 80

# Run migrations and then start the server
CMD php artisan config:cache && php artisan migrate --force && apache2-foreground
