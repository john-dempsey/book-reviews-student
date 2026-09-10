# A CLI-only container — no Apache, nothing listens on a port. This is
# where students get a terminal to run `php artisan`, `composer`, and
# `npm` commands against the same ./src bind mount that
# apache-php-container serves. Same PHP version and extension set as
# apache-php-container, so `php artisan` behaves identically to what
# the web server actually runs.
FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
        libzip-dev libpng-dev libjpeg-dev libfreetype6-dev libicu-dev libonig-dev \
        unzip git curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl \
    && rm -rf /var/lib/apt/lists/*

# Composer, straight from its own official image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Node.js + npm — needed for the Vite/Tailwind asset build Breeze's
# Blade stack ships with (`npm install && npm run build`)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# Keeps the container running (no server process of its own) so
# `docker compose exec workspace-container bash` has something to attach to.
CMD ["tail", "-f", "/dev/null"]
