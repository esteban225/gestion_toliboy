# Manual de Despliegue

## 1. Portada

**Nombre del proyecto:** Gestión Toliboy

**Versión:** 1.0.0

**Fecha:** 24 de febrero de 2026

---

## 2. Introducción

### Objetivo del manual
Este documento describe el proceso completo para desplegar el sistema Gestión Toliboy en los diferentes entornos.

### Entornos a los que aplica
- Desarrollo (DEV)
- Pruebas (QA)
- Producción (PROD)

### Público objetivo
- DevOps
- Sysadmin
- Desarrolladores

---

## 3. Arquitectura de Despliegue

### Diagrama de infraestructura
- (Agregar diagrama visual aquí, por ejemplo con Mermaid o imagen)

### Componentes involucrados
- Servidor Linux (Ubuntu recomendado)
- Nginx
- PHP (Laravel)
- MySQL
- Node.js
- Docker (opcional)

### Flujos de despliegue
- Pull de código desde repositorio
- Instalación de dependencias
- Configuración de entorno
- Migraciones y seeders
- Arranque de servicios

---

## 4. Requisitos Previos

### Sistema operativo
- Linux (Ubuntu 20.04+ recomendado)

### Dependencias
- PHP 8.1+
- Composer
- Node.js 18+
- MySQL 8+
- Nginx
- Docker y Docker Compose (opcional)

### Configuraciones previas del servidor
- Acceso SSH
- Puertos abiertos (80, 443, 3306)
- Certificados SSL (si aplica)

### Credenciales necesarias
- Acceso al repositorio Git
- Acceso a la base de datos
- Acceso a servidores y servicios externos

---

## 5. Preparación del Entorno

- Crear carpetas necesarias (`storage/`, `bootstrap/cache/`, etc.)
- Configurar variables de entorno en `.env`
- Verificar conexión a la base de datos
- Asegurar que servicios requeridos estén activos (MySQL, Nginx, etc.)

---

## 6. Proceso de Despliegue

Para cada entorno (DEV, QA, PROD):

1. **Obtención del código:**
   - Clonar o hacer pull del repositorio desde GitHub.
2. **Compilación o build:**
   - Ejecutar `composer install` y `npm install`.
   - Compilar assets con `npm run build` (o `npm run dev` para desarrollo).
3. **Instalación de dependencias:**
   - Composer y NPM.
4. **Ejecución de scripts:**
   - Migraciones: `php artisan migrate --seed`
   - Limpieza de cachés: `php artisan config:cache`, `php artisan route:cache`, etc.
5. **Configuraciones específicas:**
   - Ajustar `.env` según entorno.
6. **Verificación del despliegue:**
   - Acceder a la URL y comprobar funcionamiento.

---

## 7. Despliegue Automatizado (si aplica)

### Pipelines CI/CD
- Configuración de pipelines en GitHub Actions, GitLab CI, Jenkins, etc.

### Herramientas
- GitHub Actions (ejemplo recomendado)

### Variables utilizadas
- Variables de entorno para credenciales y configuración.

### Flujo paso a paso
1. Push a rama principal
2. Ejecución de tests
3. Build y deploy automático
4. Notificación de estado

---

## 8. Validación Post-Despliegue

- Pruebas rápidas (smoke tests) de endpoints principales
- Revisión de logs en `storage/logs/`
- Verificar accesibilidad de la aplicación
- Comprobar estado de servicios y base de datos

---

## 9. Reversión (Rollback)

- Restaurar versión anterior del código (Git checkout)
- Restaurar base de datos desde backup
- Verificar funcionamiento tras reversión
- Recomendaciones de seguridad: cambiar credenciales si hubo incidente

---

## 10. Buenas Prácticas

- Uso de versionado semántico
- Automatización de despliegues
- Monitoreo activo del sistema
- Control de cambios y auditoría

---

**Fin del manual.**
