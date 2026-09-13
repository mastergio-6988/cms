FROM php:8.4-apache
ENV APACHE_DOCUMENT_ROOT=/var/www/html/web
RUN apt-get update \
  && apt-get install -y --no-install-recommends git unzip libicu-dev libonig-dev libpng-dev libjpeg62-turbo-dev libfreetype6-dev libzip-dev libpq-dev \
  && docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install -j"$(nproc)" gd intl mbstring opcache pdo_mysql pdo_pgsql zip \
  && a2enmod rewrite headers \
  && sed -ri "s!/var/www/html!${APACHE_DOCUMENT_ROOT}!g" /etc/apache2/sites-available/*.conf /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
  && rm -rf /var/lib/apt/lists/*
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader
COPY . .
RUN mkdir -p web/sites/default/files /tmp/private \
  && cp web/sites/default/settings.render.php web/sites/default/settings.php \
  && chown -R www-data:www-data web/sites/default/files web/sites/default/settings.php
EXPOSE 80
