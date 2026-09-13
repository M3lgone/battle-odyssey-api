# syntax=docker/dockerfile:1

# Stage 1: install PHP dependencies with the official Composer image.
# Reproducible, no curl installers. Includes dev deps so
# `php artisan test` (Pest/PHPUnit) also works inside the container.
FROM composer:2 AS vendor
WORKDIR /app
ENV COMPOSER_ALLOW_SUPERUSER=1
COPY composer.json composer.lock ./
RUN composer install \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --no-interaction \
    --no-progress
COPY . .
RUN composer dump-autoload --optimize --no-interaction

# Stage 2: runtime for local/dev (php artisan serve).
# Minimal on purpose: no Nginx, PHP-FPM, Redis, Node or Sail.
FROM php:8.4-cli-bookworm
ENV DEBIAN_FRONTEND=noninteractive

# php:8.4-cli-bookworm already ships ctype, curl, dom, fileinfo, filter,
# libxml, mbstring, openssl, pcre, PDO, pdo_sqlite, session, sodium,
# tokenizer, xml, SimpleXML and sqlite3. Only the missing ones are built.
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libzip-dev \
    && docker-php-ext-install -j$(nproc) \
        bcmath \
        pcntl \
        pdo_mysql \
        zip \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# Application code first, then built vendor (wins even if host has vendor/).
COPY . .
COPY --from=vendor /app/vendor ./vendor

RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache || true

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 8000

ENTRYPOINT ["entrypoint.sh"]
# --no-reload: artisan serve spawns `php -S` with a filtered env unless this
# flag is set (only APP_ENV/PATH/... pass through). Without it, compose
# `environment:` (DB_HOST=db, ...) never reaches HTTP requests and Laravel
# silently falls back to the .env file values. It also avoids watcher
# restarts when artisan rewrites .env (e.g. key:generate).
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000", "--no-reload"]
