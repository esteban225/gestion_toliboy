# Deployment Guide - Gestión Toliboy

## Requisitos del Sistema

### Servidor de Producción
- **Sistema Operativo**: Linux (Ubuntu 20.04+ recomendado)
- **PHP**: 8.2 o superior con extensiones:
  - BCMath
  - Ctype
  - JSON
  - Mbstring
  - OpenSSL
  - PDO
  - Tokenizer
  - XML
  - Zip
  - GD
  - Curl
- **Servidor Web**: Apache 2.4+ o Nginx 1.18+
- **Base de Datos**: MySQL 8.0+ o PostgreSQL 13+
- **Node.js**: 18+ (para build de assets)
- **Composer**: 2.0+
- **Git**: Para deployment
- **SSL Certificate**: Para HTTPS

### Hardware Mínimo
- **RAM**: 2GB mínimo, 4GB recomendado
- **CPU**: 2 cores mínimo
- **Almacenamiento**: 10GB disponible
- **Ancho de Banda**: Según tráfico esperado

## Configuración del Servidor

### 1. Preparación del Sistema (Ubuntu)

```bash
# Actualizar sistema
sudo apt update && sudo apt upgrade -y

# Instalar PHP y extensiones
sudo apt install php8.2 php8.2-fpm php8.2-cli php8.2-common php8.2-mysql \
    php8.2-mbstring php8.2-xml php8.2-bcmath php8.2-json php8.2-zip \
    php8.2-gd php8.2-curl php8.2-tokenizer -y

# Instalar Nginx
sudo apt install nginx -y

# Instalar MySQL
sudo apt install mysql-server -y

# Instalar Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Instalar Node.js
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt-get install -y nodejs

# Instalar Git
sudo apt install git -y
```

### 2. Configuración de Base de Datos

```bash
# Configurar MySQL
sudo mysql_secure_installation

# Crear base de datos y usuario
sudo mysql -u root -p
```

```sql
CREATE DATABASE gestion_toliboy;
CREATE USER 'toliboy_user'@'localhost' IDENTIFIED BY 'secure_password_here';
GRANT ALL PRIVILEGES ON gestion_toliboy.* TO 'toliboy_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 3. Configuración de Nginx

```bash
# Crear configuración del sitio
sudo nano /etc/nginx/sites-available/gestion-toliboy
```

```nginx
server {
    listen 80;
    server_name tu-dominio.com www.tu-dominio.com;
    root /var/www/gestion-toliboy/public;
    
    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-XSS-Protection "1; mode=block";
    add_header X-Content-Type-Options "nosniff";
    
    index index.php;
    
    charset utf-8;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }
    
    error_page 404 /index.php;
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
    
    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

```bash
# Habilitar el sitio
sudo ln -s /etc/nginx/sites-available/gestion-toliboy /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

## Deployment del Código

### 1. Preparar Directorio

```bash
# Crear directorio del proyecto
sudo mkdir -p /var/www/gestion-toliboy
sudo chown -R $USER:www-data /var/www/gestion-toliboy
sudo chmod -R 755 /var/www/gestion-toliboy
```

### 2. Clonar Repositorio

```bash
cd /var/www
git clone https://github.com/esteban225/gestion_toliboy.git gestion-toliboy
cd gestion-toliboy
```

### 3. Instalar Dependencias

```bash
# Dependencias PHP
composer install --no-dev --optimize-autoloader

# Dependencias Node.js
npm ci
```

### 4. Configuración de Ambiente

```bash
# Copiar archivo de configuración
cp .env.example .env

# Generar clave de aplicación
php artisan key:generate

# Generar secreto JWT
php artisan jwt:secret
```

### 5. Configurar .env

```bash
nano .env
```

```env
APP_NAME="Gestión Toliboy"
APP_ENV=production
APP_KEY=base64:generated_key_here
APP_DEBUG=false
APP_URL=https://tu-dominio.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gestion_toliboy
DB_USERNAME=toliboy_user
DB_PASSWORD=secure_password_here

CACHE_STORE=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

JWT_SECRET=generated_jwt_secret_here
JWT_TTL=60
JWT_REFRESH_TTL=20160

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### 6. Build de Assets

```bash
# Compilar assets para producción
npm run build
```

### 7. Configuración de Base de Datos

```bash
# Ejecutar migraciones
php artisan migrate --force

# Ejecutar seeders (opcional)
php artisan db:seed --force
```

### 8. Configurar Permisos

```bash
# Configurar permisos correctos
sudo chown -R www-data:www-data /var/www/gestion-toliboy
sudo chmod -R 755 /var/www/gestion-toliboy
sudo chmod -R 775 /var/www/gestion-toliboy/storage
sudo chmod -R 775 /var/www/gestion-toliboy/bootstrap/cache
```

### 9. Optimizaciones de Laravel

```bash
# Optimizar aplicación
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

## Configuración SSL (Let's Encrypt)

```bash
# Instalar Certbot
sudo apt install certbot python3-certbot-nginx -y

# Obtener certificado SSL
sudo certbot --nginx -d tu-dominio.com -d www.tu-dominio.com

# Verificar renovación automática
sudo certbot renew --dry-run
```

## Configuración de Logs

### 1. Rotación de Logs

```bash
# Crear configuración de logrotate
sudo nano /etc/logrotate.d/laravel
```

```
/var/www/gestion-toliboy/storage/logs/*.log {
    daily
    missingok
    rotate 52
    compress
    delaycompress
    notifempty
    create 644 www-data www-data
    postrotate
        /bin/kill -USR1 `cat /var/run/nginx.pid 2>/dev/null` 2>/dev/null || true
    endscript
}
```

### 2. Monitoreo de Logs

```bash
# Ver logs de aplicación
tail -f /var/www/gestion-toliboy/storage/logs/laravel.log

# Ver logs de Nginx
sudo tail -f /var/log/nginx/error.log
sudo tail -f /var/log/nginx/access.log
```

## Backup Strategy

### 1. Script de Backup de Base de Datos

```bash
# Crear script de backup
sudo nano /usr/local/bin/backup-toliboy-db.sh
```

```bash
#!/bin/bash
BACKUP_DIR="/var/backups/gestion-toliboy"
DATE=$(date +%Y%m%d_%H%M%S)
DB_NAME="gestion_toliboy"
DB_USER="toliboy_user"
DB_PASS="secure_password_here"

mkdir -p $BACKUP_DIR

# Backup de base de datos
mysqldump -u$DB_USER -p$DB_PASS $DB_NAME > $BACKUP_DIR/db_backup_$DATE.sql

# Backup de archivos
tar -czf $BACKUP_DIR/files_backup_$DATE.tar.gz /var/www/gestion-toliboy \
    --exclude="/var/www/gestion-toliboy/vendor" \
    --exclude="/var/www/gestion-toliboy/node_modules" \
    --exclude="/var/www/gestion-toliboy/storage/logs"

# Eliminar backups antiguos (más de 30 días)
find $BACKUP_DIR -type f -mtime +30 -delete

echo "Backup completado: $DATE"
```

```bash
# Hacer ejecutable
sudo chmod +x /usr/local/bin/backup-toliboy-db.sh

# Agregar al crontab
sudo crontab -e
```

```crontab
# Backup diario a las 2 AM
0 2 * * * /usr/local/bin/backup-toliboy-db.sh
```

## Monitoring y Health Checks

### 1. Script de Health Check

```bash
# Crear script de monitoreo
sudo nano /usr/local/bin/toliboy-health-check.sh
```

```bash
#!/bin/bash
URL="https://tu-dominio.com/api/health"
LOGFILE="/var/log/toliboy-health.log"

# Verificar respuesta de la aplicación
if curl -f -s $URL > /dev/null; then
    echo "$(date): OK - Application is running" >> $LOGFILE
else
    echo "$(date): ERROR - Application is down!" >> $LOGFILE
    # Aquí puedes agregar notificaciones por email/slack
fi
```

### 2. Monitoreo de Procesos

```bash
# Verificar servicios críticos
sudo systemctl status nginx
sudo systemctl status php8.2-fpm
sudo systemctl status mysql
```

## Deployment Automatizado

### 1. Script de Deployment

```bash
# Crear script de deployment
nano /home/deploy/deploy-toliboy.sh
```

```bash
#!/bin/bash
set -e

PROJECT_ROOT="/var/www/gestion-toliboy"
BACKUP_DIR="/var/backups/deployment"
DATE=$(date +%Y%m%d_%H%M%S)

echo "Starting deployment..."

# Crear backup antes del deployment
echo "Creating backup..."
mkdir -p $BACKUP_DIR
mysqldump -u toliboy_user -p gestion_toliboy > $BACKUP_DIR/pre_deploy_$DATE.sql

# Ir al directorio del proyecto
cd $PROJECT_ROOT

# Poner aplicación en mantenimiento
php artisan down

# Obtener últimos cambios
git pull origin main

# Instalar/actualizar dependencias
composer install --no-dev --optimize-autoloader

# Instalar dependencias de Node
npm ci

# Build de assets
npm run build

# Ejecutar migraciones
php artisan migrate --force

# Limpiar caches
php artisan config:clear
php artisan view:clear
php artisan route:clear

# Optimizar aplicación
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Configurar permisos
sudo chown -R www-data:www-data $PROJECT_ROOT
sudo chmod -R 755 $PROJECT_ROOT
sudo chmod -R 775 $PROJECT_ROOT/storage
sudo chmod -R 775 $PROJECT_ROOT/bootstrap/cache

# Salir del modo de mantenimiento
php artisan up

echo "Deployment completed successfully!"
```

### 2. GitHub Actions (CI/CD)

```yaml
# .github/workflows/deploy.yml
name: Deploy to Production

on:
  push:
    branches: [ main ]

jobs:
  deploy:
    runs-on: ubuntu-latest
    
    steps:
    - uses: actions/checkout@v3
    
    - name: Deploy to server
      uses: appleboy/ssh-action@master
      with:
        host: ${{ secrets.HOST }}
        username: ${{ secrets.USERNAME }}
        key: ${{ secrets.KEY }}
        script: |
          cd /var/www/gestion-toliboy
          /home/deploy/deploy-toliboy.sh
```

## Troubleshooting

### 1. Problemas Comunes

#### Error 500 - Internal Server Error
```bash
# Revisar logs
tail -f /var/www/gestion-toliboy/storage/logs/laravel.log
sudo tail -f /var/log/nginx/error.log

# Verificar permisos
ls -la /var/www/gestion-toliboy/storage
ls -la /var/www/gestion-toliboy/bootstrap/cache

# Limpiar caches
php artisan config:clear
php artisan view:clear
php artisan cache:clear
```

#### Error de Base de Datos
```bash
# Verificar conexión
php artisan tinker
DB::connection()->getPdo();

# Verificar configuración
cat /var/www/gestion-toliboy/.env | grep DB_
```

#### Error de Permisos
```bash
# Corregir permisos
sudo chown -R www-data:www-data /var/www/gestion-toliboy
sudo chmod -R 755 /var/www/gestion-toliboy
sudo chmod -R 775 /var/www/gestion-toliboy/storage
sudo chmod -R 775 /var/www/gestion-toliboy/bootstrap/cache
```

### 2. Comandos Útiles

```bash
# Verificar estado de la aplicación
php artisan about

# Verificar configuración
php artisan config:show

# Limpiar todo el cache
php artisan optimize:clear

# Verificar rutas
php artisan route:list

# Verificar migraciones
php artisan migrate:status
```

## Security Checklist

- [ ] SSL/HTTPS configurado
- [ ] Permisos de archivos correctos
- [ ] Base de datos con credenciales seguras
- [ ] Variables de entorno protegidas
- [ ] Logs con rotación configurada
- [ ] Firewall configurado (puertos 80, 443, 22)
- [ ] Backups automatizados
- [ ] Actualizaciones de sistema programadas
- [ ] Monitoring configurado

## Performance Optimization

### 1. Opcache PHP
```bash
# Configurar en php.ini
sudo nano /etc/php/8.2/fpm/php.ini
```

```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0
```

### 2. Nginx Gzip
```nginx
# Agregar a configuración de Nginx
gzip on;
gzip_vary on;
gzip_min_length 1024;
gzip_comp_level 6;
gzip_types text/plain text/css application/json application/javascript text/xml application/xml application/xml+rss text/javascript;
```

---

*Esta guía de deployment cubre todos los aspectos necesarios para poner en producción el sistema Gestión Toliboy de manera segura y eficiente.*