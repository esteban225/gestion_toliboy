# Usa una imagen base de PHP 8.2 con FrankenPHP, que es moderna y eficiente.
FROM dunglas/frankenphp:1.1.2-php8.2-bookworm

# Instala dependencias del sistema necesarias para las extensiones de PHP.
# Incluimos las librerías para gd, zip y bcmath.
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && rm -rf /var/lib/apt/lists/*

# Instala las extensiones de PHP que tu proyecto necesita.
# Aquí es donde añadimos gd, bcmath, pdo_mysql y zip.
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install pdo_mysql zip bcmath

# Copia Composer desde su imagen oficial para poder usarlo.
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Establece el directorio de trabajo dentro del contenedor.
WORKDIR /app

# Copia los archivos de tu proyecto al contenedor.
# Usamos .dockerignore para excluir archivos innecesarios como node_modules.
COPY . .

# Instala las dependencias de Composer.
# El --ignore-platform-reqs es una medida de seguridad extra.
RUN composer install --optimize-autoloader --no-dev --no-interaction --ignore-platform-reqs

# Establece los permisos correctos para las carpetas de Laravel.
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache

# Expone el puerto que usará la aplicación.
EXPOSE 80

# Comando para iniciar el servidor de FrankenPHP.
CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
