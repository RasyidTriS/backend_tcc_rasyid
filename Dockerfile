FROM composer:2 AS vendor
WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts

COPY . .
RUN composer dump-autoload --optimize && php artisan package:discover --ansi

FROM php:8.3-apache
WORKDIR /var/www/html

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        libicu-dev \
        libzip-dev \
        unzip \
    && docker-php-ext-install \
        intl \
        pdo_mysql \
        zip \
        opcache \
    && a2enmod rewrite \
    && sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!<VirtualHost \*:80>!<VirtualHost *:${PORT}>!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!Listen 80!Listen ${PORT}!g' /etc/apache2/ports.conf \
    && rm -rf /var/lib/apt/lists/*

COPY --from=vendor /app /var/www/html
COPY deploy/start-apache.sh /usr/local/bin/start-apache

RUN chmod +x /usr/local/bin/start-apache \
    && mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/testing storage/framework/views bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

ENV PORT=8080
EXPOSE 8080

CMD ["start-apache"]
