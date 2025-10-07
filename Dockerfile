# Dockerfile para Laravel
FROM php:8.3-fpm

# Instala dependencias del sistema
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
    && docker-php-ext-install zip

# Instala extensiones de PHP
RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

# Instala Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Instalación de Node.js y npm
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
RUN apt-get install -y nodejs

# Establece el directorio de trabajo
WORKDIR /var/www

# Copia el código fuente
COPY . /var/www

# Da permisos a la carpeta de almacenamiento
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Instalar dependencias y compilar assets después de copiar el código fuente
RUN npm install && npm run build

# Expone el puerto 9000
EXPOSE 9000

CMD ["php-fpm"]
