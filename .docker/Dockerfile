FROM php:8.4-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    curl gnupg libicu-dev libzip-dev libpng-dev libjpeg-dev libfreetype6-dev \
    nginx supervisor \
    && curl -sL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install intl bcmath pdo_mysql zip gd opcache \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

# Configure opcache for production
RUN echo "opcache.enable=1\n\
opcache.memory_consumption=128\n\
opcache.interned_strings_buffer=8\n\
opcache.max_accelerated_files=10000\n\
opcache.validate_timestamps=0\n\
opcache.save_comments=1\n\
opcache.fast_shutdown=1" > /usr/local/etc/php/conf.d/opcache.ini

WORKDIR /var/www/html

# Copy application files
COPY --chown=www-data:www-data . /var/www/html/

# Copy configs
COPY .docker/php/website.ini /usr/local/etc/php/conf.d/
COPY .docker/nginx.conf /etc/nginx/sites-available/default
COPY .docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Install dependencies and build assets
RUN composer install --optimize-autoloader --no-dev \
    && npm ci && npm run build \
    && rm -rf node_modules

# Cache Laravel config/routes/views
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# Ensure storage and cache directories are writable
RUN chown -R www-data:www-data storage bootstrap/cache

EXPOSE 8080

CMD ["supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
