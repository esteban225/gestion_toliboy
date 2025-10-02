# ============================
# Etapa 1: Build de assets con Node.js (Vite)
# ============================
FROM node:20-alpine AS builder

WORKDIR /var/www/html

# Copiar archivos de configuración de Node
COPY package.json package-lock.json vite.config.js ./

# Instalar dependencias de Node
RUN npm install

# Copiar el resto de los archivos de frontend
COPY resources/ resources/

# Compilar assets para producción
RUN npm run build

# ============================
# Etapa 2: Imagen final de PHP + Composer
# ============================
FROM php:8.2-cli

# Instalar dependencias del sistema necesarias para compilar extensiones PHP
RUN apt-get update && apt-get install -y \
    curl \
    git \
    unzip \
    libfreetype6-dev \
    libjpeg-dev \
    libonig-dev \
    libpng-dev \
    libxml2-dev \
    libzip-dev \
    libwebp-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install \
       bcmath \
       exif \
       gd \
       mbstring \
       pcntl \
       pdo_mysql \
       xml \
       zip \
    && rm -rf /var/lib/apt/lists/*

# Instalar Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Crear directorio de la aplicación
WORKDIR /var/www/html

# Copiar composer files primero para aprovechar la cache de Docker
COPY composer.json composer.lock ./

# Instalar dependencias de PHP
RUN composer install --no-dev --prefer-dist --no-progress --no-interaction --optimize-autoloader

# Copiar el resto de la aplicación
COPY . .

# Copiar los assets compilados desde la etapa 'builder'
COPY --from=builder /var/www/html/public/build ./public/build

# Generar clave de app y cachear config/rutas/vistas
RUN php artisan key:generate --force \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Exponer puerto (Railway usa PORT)
EXPOSE 8000
ENV PORT=8000

# Comando de arranque
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=${PORT}"]
