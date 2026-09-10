# Serves the Laravel application. Same base pattern as the
# CC-Y1-WebDev apache-php-mysql image, extended with the PHP extensions
# Laravel itself requires.
FROM php:8.4-apache

RUN a2enmod rewrite

RUN apt-get update && apt-get install -y \
        libzip-dev libpng-dev libjpeg-dev libfreetype6-dev libicu-dev libonig-dev \
        unzip git curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl \
    && rm -rf /var/lib/apt/lists/*

RUN pecl install xdebug && docker-php-ext-enable xdebug

# Document root points at Laravel's public/ directory, not the project root
COPY ./config/httpd.conf /etc/apache2/sites-available/000-default.conf

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

COPY ./config/xdebug.ini          /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
COPY ./config/error_reporting.ini /usr/local/etc/php/conf.d/error_reporting.ini

# Fixes storage/ and bootstrap/cache/ ownership on every container start —
# see the script's own comment for why this runs at start rather than build
COPY ./entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh
ENTRYPOINT ["entrypoint.sh"]
CMD ["apache2-foreground"]
