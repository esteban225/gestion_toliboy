# =========================
# Etapa 1: Build de dependencias y assets
# =========================
FROM php:8.3-fpm AS builder

# Instala dependencias del sistema necesarias para PHP y Node
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-configure zip \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip

# Instala Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Instala Node.js 20 y npm
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && \
    apt-get install -y nodejs && npm install -g npm@latest

# Establece el directorio de trabajo
WORKDIR /var/www

# Copia solo los archivos necesarios antes del build
COPY package*.json ./
COPY composer.json composer.lock ./

# Instala dependencias de PHP y Node
RUN composer install --no-dev --no-scripts --prefer-dist --optimize-autoloader
RUN npm install

# Copia el resto del código fuente
COPY . .

# Compila los assets del frontend (Laravel Mix o Vite)
RUN npm run build

# Permisos
RUN chown -R www-data:www-data storage bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache


# =========================
# Etapa 2: Imagen final
# =========================
FROM php:8.3-fpm

# Copia extensiones y dependencias del builder
COPY --from=builder /usr/local/lib/php/extensions /usr/local/lib/php/extensions
COPY --from=builder /usr/local/etc/php/conf.d /usr/local/etc/php/conf.d

# Copia composer y Node.js opcionalmente (si deseas)
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Establece el directorio de trabajo
WORKDIR /var/www

# Copia el proyecto ya compilado del builder
COPY --from=builder /var/www /var/www

# Permisos finales
RUN chown -R www-data:www-data /var/www && \
    chmod -R 775 /var/www/storage /var/www/bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
