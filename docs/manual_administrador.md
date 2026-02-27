# Manual de Administrador

## 1. Portada

**Nombre del sistema:** Gestión Toliboy

**Versión:** 1.0.0

**Equipo técnico:**
- Esteban225 (Propietario del repositorio)
- David-dev (Colaborador)

---

## 2. Introducción

### Objetivo
Este manual proporciona las directrices necesarias para la operación y mantenimiento del sistema Gestión Toliboy, dirigido a administradores técnicos.

### Responsabilidades del administrador
- Garantizar la disponibilidad y correcto funcionamiento del sistema.
- Gestionar usuarios y roles.
- Supervisar la seguridad y el acceso.
- Realizar respaldos y restauraciones de la base de datos.
- Monitorear el sistema y responder ante incidentes.

### Alcance del manual
Este documento cubre la administración, operación, monitoreo, mantenimiento y solución de problemas del sistema.

---

## 3. Arquitectura del Sistema (Vista General)

### Componentes principales
- Backend PHP (Laravel)
- Base de datos MySQL
- Servidor web Nginx
- Sistema de colas y jobs

### Módulos
- Gestión de usuarios
- Inventario y productos
- Formularios y respuestas
- Auditoría y logs

### Integraciones
- Base de datos MySQL
- APIs internas
- Servicios de notificaciones

---

## 4. Requisitos del Servidor

### Hardware recomendado
- CPU: 2 núcleos o más
- RAM: 4 GB mínimo
- Almacenamiento: 20 GB SSD

### Sistema operativo
- Linux (Ubuntu 20.04+ recomendado)

### Dependencias
- PHP 8.1+
- Composer
- Node.js 18+
- MySQL 8+
- Nginx
- Docker (opcional para despliegue)

### Versiones necesarias
- PHP: >=8.1
- Node.js: >=18
- MySQL: >=8

---

## 5. Gestión de Usuarios

### Crear, editar y eliminar usuarios
- Acceder al módulo de usuarios desde el panel de administración.
- Utilizar las opciones para crear, editar o eliminar usuarios según los permisos asignados.

### Roles y permisos
- El sistema implementa roles (admin, usuario, etc.) definidos en la base de datos y gestionados por el middleware `RoleAuthorization`.

### Políticas de seguridad
- Contraseñas seguras y expiración periódica.
- Bloqueo tras intentos fallidos.

---

## 6. Configuración del Sistema

### Archivos de configuración
- `config/` (archivos PHP de configuración)
- `.env` (variables de entorno)

### Variables de entorno
- Configurar base de datos, correo, claves secretas, etc.

### Ajustes por entorno
- Utilizar archivos `.env` específicos para dev, QA y prod.

---

## 7. Administración de la Base de Datos

### Conexiones
- Configuradas en `config/database.php` y `.env`.

### Respaldos (backups)
- Utilizar `mysqldump` o herramientas de Docker para generar respaldos periódicos.

### Restauración
- Restaurar con `mysql` o desde Docker según corresponda.

### Mantenimiento programado
- Revisar integridad y optimización de tablas periódicamente.

---

## 8. Monitoreo del Sistema

### Logs y auditoría
- Revisar logs en `storage/logs/`.
- Auditoría de acciones en la base de datos (`app/Models/AuditLog.php`).

### Métricas
- Integrar herramientas como Grafana o Prometheus si es necesario.

### Alertas
- Configurar alertas por correo o sistemas externos.

### Herramientas recomendadas
- Laravel Telescope, Grafana, Prometheus, Sentry.

---

## 9. Mantenimiento

### Actualizaciones del sistema
- Actualizar dependencias con Composer y NPM.
- Seguir buenas prácticas de versionado.

### Gestión de parches
- Aplicar parches de seguridad y actualizaciones críticas.

### Limpieza y optimización
- Limpiar logs antiguos y optimizar base de datos regularmente.

---

## 10. Recuperación ante Fallos

### Estrategia de recuperación
- Mantener respaldos recientes y procedimientos claros de restauración.

### Procedimientos paso a paso
- Documentar y probar los procedimientos de recuperación.

### Escenarios de emergencia
- Fallo de base de datos, pérdida de archivos, caídas del sistema.

---

## 11. Solución de Problemas Técnicos

### Errores comunes
- Problemas de conexión a base de datos
- Errores de permisos
- Fallos en jobs o colas

### Diagnóstico
- Revisar logs y mensajes de error.
- Utilizar comandos Artisan para diagnóstico (`php artisan ...`).

### Procedimientos de soporte
- Escalar problemas críticos al equipo técnico.
- Documentar incidencias y soluciones.

---

**Fin del manual.**
