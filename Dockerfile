# Etapa 1: Build con Composer
FROM php:8.2-cli AS build

WORKDIR /app

# Instalar dependencias necesarias (incluyendo GD) antes de composer install
RUN apt-get update && apt-get install -y \
    unzip git curl libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd

# Copiar archivos composer
COPY composer.json composer.lock ./

# Instalar dependencias PHP
RUN composer install --no-dev --prefer-dist --no-progress --no-interaction --optimize-autoloader

# Copiar el resto del proyecto
COPY . .



# Etapa 2: Producción
FROM php:8.2-fpm

WORKDIR /var/www/html

# Instalar extensiones necesarias
RUN apt-get update && apt-get install -y \
    unzip git curl libpng-dev libjpeg-dev libfreetype6-dev libonig-dev libxml2-dev zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

# Copiar vendor desde la etapa de build
COPY --from=build /app/vendor ./vendor

# Copiar código de la app
COPY . .

# Permisos para Laravel
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
