# =========================
# Etapa 1: Build de dependencias y assets
# =========================
FROM php:8.3-fpm AS builder

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instala Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Instala Node.js 20 y npm
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && \
    apt-get install -y nodejs && npm install -g npm@latest

WORKDIR /var/www

# Copia manifests primero (cache de capas)
COPY package*.json ./
COPY composer.json composer.lock ./

# Instala dependencias
RUN composer install --no-dev --no-scripts --prefer-dist --optimize-autoloader
RUN npm install

# Copia el resto del proyecto
COPY . .

# Build frontend
RUN npm run build

# =========================
# Etapa 2: Imagen final
# =========================
FROM php:8.3-fpm

# Librerías runtime necesarias para gd y zip
RUN apt-get update && apt-get install -y \
    libpng16-16 \
    libjpeg62-turbo \
    libfreetype6 \
    libzip5 \
    zlib1g \
    libonig5 \
    libxml2 \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Copia extensiones PHP y configs
COPY --from=builder /usr/local/lib/php/extensions /usr/local/lib/php/extensions
COPY --from=builder /usr/local/etc/php/conf.d /usr/local/etc/php/conf.d

# Composer (opcional pero útil)
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copia la app ya compilada
COPY --from=builder /var/www /var/www

# 👉 PERMISOS (FORMA CORRECTA)
RUN mkdir -p /var/www/storage/logs /var/www/bootstrap/cache && \
    chown -R www-data:www-data /var/www && \
    chmod -R 775 /var/www/storage /var/www/bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
