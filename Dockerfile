FROM php:8.3-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends libfreetype6-dev libjpeg62-turbo-dev libpng-dev libwebp-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install gd pdo_mysql \
    && printf 'upload_max_filesize=32M\npost_max_size=40M\nmemory_limit=256M\n' > /usr/local/etc/php/conf.d/uploads.ini \
    && a2enmod rewrite \
    && sed -ri 's!AllowOverride None!AllowOverride All!g' /etc/apache2/apache2.conf \
    && mkdir -p /var/www/html/uploads \
    && chown www-data:www-data /var/www/html/uploads \
    && rm -rf /var/lib/apt/lists/*

COPY src/ /var/www/html/

WORKDIR /var/www/html