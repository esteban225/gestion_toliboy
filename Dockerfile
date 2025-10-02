# Etapa 1: Build de assets con Node.js (Vite)
FROM node:18-alpine AS builder

WORKDIR /var/www/html

# Copiar archivos de configuración de Node
COPY package.json package-lock.json vite.config.js ./

# Instalar dependencias de Node
RUN npm install

# Copiar el resto de los archivos de frontend
COPY resources/ resources/

# Compilar assets para producción
RUN npm run build

# ---

# Etapa 2: Imagen final de PHP
FROM php:8.2-cli

# Instalar dependencias del sistema y extensiones PHP comunes para Laravel (ordenadas alfabéticamente)
RUN apt-get update && apt-get install -y \
    bcmath \
    curl \
    exif \
    gd \
    git \
    libfreetype6-dev \
    libjpeg-dev \
    libonig-dev \
    libpng-dev \
    libxml2-dev \
    libzip-dev \
    mbstring \
    pcntl \
    pdo \
    pdo_mysql \
    unzip \
    xml \
    zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql zip mbstring exif pcntl bcmath gd xml \
    && rm -rf /var/lib/apt/lists/*

# Instalar Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Crear directorio de la aplicación
WORKDIR /var/www/html

# Copiar composer files primero para aprovechar el cache de Docker
COPY composer.json composer.lock ./

# Instalar dependencias de PHP (sin scripts para evitar errores de falta de .env)
RUN composer install --no-dev --prefer-dist --no-progress --no-interaction --optimize-autoloader

# Copiar el resto de la aplicación
COPY . .

# Copiar los assets compilados desde la etapa 'builder'
COPY --from=builder /var/www/html/public/build ./public/build

# Ejecutar scripts de composer, generar claves/caché y ajustar permisos
RUN composer install --no-dev --prefer-dist --no-progress --no-interaction --optimize-autoloader \
    && php artisan key:generate --force \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Exponer puerto (Railway proporciona PORT env var en tiempo de ejecución)
EXPOSE 8000

# Usar variable de entorno PORT si existe, sino 8000
ENV PORT=8000

# Comando de arranque: usar el servidor embebido de PHP apuntando al directorio public
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=${PORT}"]
