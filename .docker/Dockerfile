FROM php:8.2-fpm

# Install necessary dependencies
RUN apt-get update && apt-get install -y curl gnupg libicu-dev libzip-dev supervisor \
    && curl -sL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y apt-transport-https curl debian-archive-keyring debian-keyring nodejs \
    && apt-get clean \
    && docker-php-ext-install intl bcmath pdo_mysql zip \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir /usr/bin --filename composer

# Set the working directory
WORKDIR /var/www/html

# Copy the application files
COPY --chown=www-data:www-data  . /var/www/html/

# Copy project-specific PHP configuration
COPY .docker/php/website.ini /usr/local/etc/php/conf.d/

# Copy the supervisord configuration file
COPY .docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Install PHP dependencies
RUN composer install && npm install && npm run build

# Expose the necessary ports
EXPOSE 80
EXPOSE 443

# Start supervisord
CMD ["supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
