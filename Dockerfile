# syntax=docker/dockerfile:1.7

FROM php:8.4-apache

ENV APACHE_DOCUMENT_ROOT=/var/www/html \
    COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_MEMORY_LIMIT=-1 \
    DEBUG=false

# Dependencias del sistema y extensiones PHP
RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libicu-dev \
        libzip-dev \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
        libpq-dev \
        zlib1g-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-configure intl \
    && docker-php-ext-install -j"$(nproc)" \
        intl \
        pdo_mysql \
        pdo_pgsql \
        gd \
        zip \
        opcache \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Configuración de Apache y PHP
COPY docker/apache-vhost.conf /etc/apache2/sites-available/000-default.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/zz-app.ini

WORKDIR /var/www/html

# Instalar dependencias primero (capa cacheada si no cambian composer.json/lock)
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --no-scripts \
    --no-autoloader \
    --prefer-dist

# Copiar el código de la app
COPY --chown=www-data:www-data . /var/www/html

# Generar autoload optimizado con el código completo presente
RUN composer dump-autoload --no-dev --optimize --classmap-authoritative

# app_local.php basado en el example (lee DATABASE_URL/SECURITY_SALT de env)
RUN cp config/app_local.example.php config/app_local.php \
    && mkdir -p logs tmp/cache/models tmp/cache/persistent tmp/cache/views tmp/sessions tmp/tests \
    && chown -R www-data:www-data logs tmp vendor \
    && chmod -R 775 logs tmp

# Entrypoint: corre migraciones y arranca Apache
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["apache2-foreground"]
