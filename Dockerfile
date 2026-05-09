FROM php:8.2-apache

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libzip-dev \
    unzip \
    git \
    && docker-php-ext-install pdo_mysql intl zip

# Instalar Composer desde la imagen oficial
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN a2enmod rewrite

RUN sed -ri -e 's!/var/www/html!/var/www/html/webroot!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!/var/www/html/webroot!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Copiar archivos de dependencias primero para aprovechar el caché de capas
COPY composer.json composer.lock /var/www/html/

WORKDIR /var/www/html

# Instalar dependencias
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Copiar el resto del código
COPY . .

# Finalizar la instalación de Composer y preparar directorios
RUN composer dump-autoload --optimize --no-dev \
    && mkdir -p /var/www/html/tmp/cache/models /var/www/html/tmp/cache/persistent /var/www/html/tmp/cache/views /var/www/html/logs \
    && chown -R www-data:www-data /var/www/html/tmp /var/www/html/logs \
    && chmod -R 775 /var/www/html/tmp /var/www/html/logs

EXPOSE 80
