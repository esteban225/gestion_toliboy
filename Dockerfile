# Usa una imagen base con FrankenPHP + PHP 8.2
FROM dunglas/frankenphp:1.1.2-php8.2-bookworm

# Instala dependencias del sistema necesarias
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && rm -rf /var/lib/apt/lists/*

# Instala extensiones de PHP (gd, zip, bcmath, pdo_mysql)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install pdo_mysql zip bcmath

# Copia Composer desde su imagen oficial
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Establece directorio de trabajo
WORKDIR /app

# Copia archivos del proyecto (usando .dockerignore)
COPY . .

# Instala dependencias de Composer (producción)
RUN composer install --optimize-autoloader --no-dev --no-interaction --ignore-platform-reqs

# Permisos correctos para Laravel
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache

# Expone el puerto asignado dinámicamente por Railway
EXPOSE ${PORT}

# Comando de inicio (Caddy/FrankenPHP)
CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile", "--adapter", "caddyfile"]
