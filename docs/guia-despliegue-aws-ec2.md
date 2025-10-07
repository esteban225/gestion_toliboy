# Guía de Despliegue en AWS EC2 - Proyecto Toliboy

Esta guía detalla los pasos necesarios para desplegar el proyecto Toliboy en una instancia EC2 de Amazon Web Services (AWS) utilizando Docker.

## Paso 1: Configurar y lanzar una instancia EC2 en AWS

1. Inicie sesión en la consola de AWS y vaya al servicio EC2
2. Haga clic en "Launch Instance" (Lanzar instancia)
3. Seleccione una AMI de Amazon Linux 2 o Ubuntu Server (recomendado: Ubuntu Server 22.04 LTS)
4. Elija un tipo de instancia (mínimo t2.micro para pruebas, t2.small o mayor para producción)
5. Configure los detalles de la instancia según sus necesidades
6. En la configuración del grupo de seguridad, permita:
   - SSH (Puerto 22)
   - HTTP (Puerto 80)
   - HTTPS (Puerto 443)
   - TCP personalizado (Puerto 3306) si necesita acceso remoto a MySQL
7. Revise y lance la instancia
8. Cree o seleccione un par de claves para conectarse a la instancia

## Paso 2: Conectarse a la instancia EC2

```bash
ssh -i "/ruta/a/su-archivo-clave.pem" ubuntu@su-direccion-ec2.compute.amazonaws.com
```

## Paso 3: Instalar Docker y Docker Compose en la instancia

```bash
# Actualizar el sistema
sudo apt update && sudo apt upgrade -y

# Instalar dependencias necesarias
sudo apt install -y apt-transport-https ca-certificates curl software-properties-common

# Agregar la clave GPG oficial de Docker
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo apt-key add -

# Agregar el repositorio de Docker a las fuentes de APT
sudo add-apt-repository "deb [arch=amd64] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable"

# Instalar Docker CE
sudo apt update
sudo apt install -y docker-ce docker-ce-cli containerd.io

# Agregar el usuario actual al grupo Docker (para no tener que usar sudo)
sudo usermod -aG docker ${USER}
# Aplicar cambios de grupo (o cierre sesión y vuelva a iniciar)
newgrp docker

# Instalar Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/download/v2.20.0/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose

# Verificar la instalación
docker --version
docker-compose --version
```

## Paso 4: Clonar el repositorio en la instancia EC2

```bash
# Instalar Git si no está instalado
sudo apt install -y git

# Clonar el repositorio (reemplace con su URL de repositorio)
git clone https://github.com/esteban225/gestion_toliboy.git

# Navegar al directorio del proyecto
cd gestion_toliboy
```

## Paso 5: Configurar el archivo .env

```bash
# Copiar el archivo .env.example a .env
cp .env.example .env

# Editar el archivo .env para configurar la conexión a la base de datos
nano .env
```

Asegúrese de configurar estos valores en el archivo .env:

```ini
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=toliboy
DB_USERNAME=toliboy
DB_PASSWORD=toliboy

APP_URL=http://su-direccion-ec2.compute.amazonaws.com
```

## Paso 6: Actualizar la configuración de Nginx

Actualice el archivo nginx.conf para usar el nombre de servidor correcto:

```bash
nano nginx.conf
```

Cambie la línea `server_name 18.188.114.143;` por su dirección IP o dominio:

```nginx
server_name su-direccion-ec2.compute.amazonaws.com;
```

## Paso 7: Construir y ejecutar los contenedores Docker

```bash
# Construir los contenedores
docker-compose build

# Iniciar los servicios en segundo plano
docker-compose up -d
```

## Paso 8: Configurar Laravel dentro del contenedor

```bash
# Entrar al contenedor de la aplicación
docker-compose exec app bash

# Instalar dependencias de Composer
composer install

# Generar una clave de aplicación
php artisan key:generate

# Generar clave JWT si está utilizando autenticación JWT
php artisan jwt:secret

# Ejecutar migraciones
php artisan migrate

# Ejecutar seeders (si es necesario)
php artisan db:seed

# Configurar almacenamiento
php artisan storage:link

# Configurar permisos (dentro del contenedor)
chmod -R 775 storage bootstrap/cache

# Salir del contenedor
exit
```

## Paso 9: Verificar que la aplicación está en funcionamiento

Abra un navegador y visite:
```
http://su-direccion-ec2.compute.amazonaws.com
```

## Paso 10: Configurar tareas programadas (si es necesario)

Si su aplicación utiliza tareas programadas Laravel, necesita configurar un cron job:

```bash
# Entrar al contenedor
docker-compose exec app bash

# Abrir el crontab
crontab -e

# Agregar esta línea
* * * * * cd /var/www && php artisan schedule:run >> /dev/null 2>&1

# Guardar y salir
```

## Paso 11: Configurar SSL con Certbot (opcional pero recomendado)

```bash
# Instalar Certbot
sudo apt install -y certbot python3-certbot-nginx

# Obtener certificado SSL
sudo certbot --nginx -d su-direccion-ec2.compute.amazonaws.com
```

## Paso 12: Configurar copia de seguridad (opcional)

```bash
# Crear un directorio para backups
mkdir -p ~/backups

# Script simple de backup para la base de datos
docker-compose exec db sh -c 'mysqldump -u toliboy -ptoliboy toliboy' > ~/backups/backup-$(date +%Y%m%d).sql
```

## Paso 13: Configurar monitoreo (opcional)

Considere instalar herramientas de monitoreo como Prometheus, Grafana o utilizar servicios como AWS CloudWatch.

## Comandos útiles para la gestión

```bash
# Ver los logs de los contenedores
docker-compose logs

# Ver logs de un servicio específico
docker-compose logs app
docker-compose logs nginx
docker-compose logs db

# Reiniciar los servicios
docker-compose restart

# Detener los servicios
docker-compose down

# Iniciar los servicios
docker-compose up -d

# Ver contenedores en ejecución
docker ps

# Ver uso de recursos
docker stats
```

## Solución de problemas comunes

### El servidor no responde en el puerto 80
- Verifique que el grupo de seguridad tenga abierto el puerto 80
- Compruebe que Nginx esté en ejecución: `docker-compose ps nginx`
- Revise los logs de Nginx: `docker-compose logs nginx`

### Errores de conexión a la base de datos
- Verifique que el contenedor de la base de datos esté en ejecución: `docker-compose ps db`
- Revise los logs de MySQL: `docker-compose logs db`
- Compruebe la configuración en el archivo .env

### Problemas de permisos en archivos
- Ejecute dentro del contenedor: `chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache`

## Mantenimiento

### Actualización de la aplicación

```bash
# Entrar al directorio del proyecto
cd ~/gestion_toliboy

# Obtener los últimos cambios
git pull

# Reconstruir contenedores si es necesario
docker-compose build

# Reiniciar contenedores
docker-compose down
docker-compose up -d

# Ejecutar migraciones si hay cambios en la base de datos
docker-compose exec app php artisan migrate

# Limpiar caché
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear
```