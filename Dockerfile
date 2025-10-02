# Image base: PHP with Composer
FROM php:8.2-cli

# Instalar dependencias del sistema y extensiones PHP comunes para Laravel
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libonig-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libxml2-dev \
    zip \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql zip mbstring exif pcntl bcmath gd xml \
    && rm -rf /var/lib/apt/lists/*

# Instalar Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Crear directorio de la aplicación
WORKDIR /var/www/html

# Copiar composer files primero para aprovechar el cache de Docker
COPY composer.json composer.lock ./

# Instalar dependencias de PHP
RUN composer install --no-dev --prefer-dist --no-progress --no-scripts --no-interaction --optimize-autoloader || true

# Copiar el resto de la aplicación
COPY . .

# Ejecutar composer install final (ahora con los scripts si existen)
RUN composer install --no-dev --prefer-dist --no-progress --no-interaction --optimize-autoloader

# Generar key y cache (si se necesita; allow failure)
RUN php artisan key:generate || true
RUN php artisan config:cache || true

# Ajustar permisos del storage y bootstrap/cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || true

# Exponer puerto (Railway proporciona PORT env var en tiempo de ejecución)
EXPOSE 8000

# Usar variable de entorno PORT si existe, sino 8000
ENV PORT=8000

# Comando de arranque: usar el servidor embebido de PHP apuntando al directorio public
CMD ["sh", "-c", "php -S 0.0.0.0:${PORT} -t public"]
