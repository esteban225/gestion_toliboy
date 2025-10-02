# Etapa 1: Construcción
FROM composer:2 AS build

WORKDIR /app

# Copiar archivos composer primero (para aprovechar la cache)
COPY composer.json composer.lock ./

# Instalar dependencias
RUN composer install --no-dev --prefer-dist --no-progress --no-interaction --optimize-autoloader

# Copiar el resto del código Laravel
COPY . .

# Etapa 2: Producción
FROM php:8.2-fpm

WORKDIR /var/www/html

# Instalar extensiones necesarias
RUN apt-get update && apt-get install -y \
    unzip git curl libpng-dev libjpeg-dev libfreetype6-dev libonig-dev libxml2-dev zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

# Copiar dependencias de vendor desde la etapa de build
COPY --from=build /app/vendor ./vendor

# Copiar código de la app
COPY . .

# Dar permisos de escritura al storage y bootstrap/cache
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Exponer puerto
EXPOSE 8000

# Comando de inicio
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
