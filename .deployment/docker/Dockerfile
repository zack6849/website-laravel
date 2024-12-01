FROM php:8.2-fpm

# Install necessary dependencies
RUN apt-get update && apt-get install -y curl gnupg supervisor libicu-dev libzip-dev \
    && curl -sL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs htop debian-keyring debian-archive-keyring apt-transport-https curl


# Install Caddy
RUN curl -1sLf 'https://dl.cloudsmith.io/public/caddy/stable/gpg.key' | gpg --dearmor -o /usr/share/keyrings/caddy-stable-archive-keyring.gpg \
    && curl -1sLf 'https://dl.cloudsmith.io/public/caddy/stable/debian.deb.txt' | tee /etc/apt/sources.list.d/caddy-testing.list \
    && apt-get update \
    && apt-get -y install caddy \
    && docker-php-ext-install intl bcmath pdo_mysql zip \
    && apt-get clean

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir /usr/bin --filename composer
RUN composer --version

COPY .deployment/caddy/Caddyfile /etc/caddy/Caddyfile

# Set the working directory
WORKDIR /var/www/html
# Copy the application files
COPY . /var/www/html/

COPY .deployment/php/website.ini /usr/local/etc/php/conf.d/

# Install PHP dependencies
RUN composer install

# Install Node.js dependencies and build assets
RUN npm install && npm run build

# Expose the necessary ports
EXPOSE 80
EXPOSE 443

# Copy the supervisord configuration file
COPY .deployment/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Start supervisord
CMD ["supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
