# Gestión Toliboy - Sistema de Gestión Laravel

## Descripción General

Gestión Toliboy es una aplicación web desarrollada con Laravel 12 que proporciona una plataforma completa para la gestión de procesos de producción, inventario, formularios personalizables y registros de trabajo. La aplicación cuenta con una API RESTful protegida mediante autenticación JWT y control de acceso basado en roles.

## Características Principales

- **Autenticación JWT** con roles de usuario (Developer, Admin)
- **Gestión de productos** y materias primas
- **Control de lotes de producción**
- **Gestión de inventario** con registro de movimientos
- **Formularios personalizables** con campos dinámicos
- **Registro de horas de trabajo**
- **Sistema de notificaciones**
- **API RESTful** para integración con otros sistemas

## Requisitos del Sistema

- PHP 8.3 o superior
- Composer 2.0+
- Node.js 18+ y NPM
- MySQL 8.0+ o PostgreSQL 13+ (recomendado)
- Servidor web (Apache/Nginx)

## Instalación y Configuración

### Requisitos Previos

Asegúrese de tener instalado:

- PHP 8.3+
- Composer
- Node.js y NPM
- MySQL o PostgreSQL

### Pasos de Instalación

1. **Clonar el repositorio**:

   ```bash
   git clone https://github.com/yourusername/gestion-toliboy.git
   cd gestion-toliboy
   ```

2. **Instalar dependencias PHP** (IMPORTANTE para PHP 8.3):

   ```bash
   composer update --no-interaction
   composer install --no-interaction
   ```

   > ⚠️ **IMPORTANTE**: El comando `composer update` puede tardar 5-6 minutos. NUNCA lo cancele. Configure un timeout de al menos 10 minutos.

3. **Instalar dependencias Node.js**:

   ```bash
   npm install
   npm audit fix
   ```

4. **Configurar el entorno**:

   ```bash
   cp .env.example .env
   php artisan key:generate
   php artisan jwt:secret
   ```

5. **Configurar la base de datos SQLite** (para desarrollo rápido):

   ```bash
   touch database/database.sqlite
   php artisan session:table
   php artisan migrate
   ```

6. **Compilar assets**:

   ```bash
   npm run build
   ```

7. **Iniciar servidor de desarrollo**:

   ```bash
   php artisan serve --host=0.0.0.0 --port=8000
   ```

   Acceder a: <http://localhost:8000>

## Desarrollo

### Entorno de desarrollo con hot reload

Para desarrollo con recarga automática:

1. Configure en su archivo .env:

   ```
   LARAVEL_BYPASS_ENV_CHECK=1
   ```

2. Ejecute:

   ```bash
   composer run dev
   ```

Este comando iniciará el servidor, el listener de colas y Vite concurrentemente.

### Formateo de Código

El proyecto utiliza Laravel Pint para mantener un estilo de código consistente:

```bash
# Verificar formato sin modificar archivos
vendor/bin/pint --test

# Corregir formato
vendor/bin/pint
```

> ⚠️ **CRÍTICO**: Siempre ejecute Pint antes de realizar commits. La base de código actual tiene 37+ violaciones de estilo que causarán fallos en CI si no se corrigen.

### Pruebas

```bash
# Ejecutar todas las pruebas
php artisan test

# Ejecutar pruebas específicas
php artisan test --filter=ProductTest
```

## Estructura de la API

Todas las rutas de API están disponibles bajo el prefijo `/api` y la mayoría requieren autenticación JWT.

### Endpoints Principales

#### Autenticación

- **POST /api/register**: Registro de usuario
- **POST /api/login**: Iniciar sesión (obtener token JWT)
- **POST /api/logout**: Cerrar sesión (requiere autenticación)
- **POST /api/refresh**: Refrescar token JWT

#### Usuarios y Datos Personales

- **Resource /api/users**: Gestión de usuarios
- **Resource /api/userData**: Datos personales de los usuarios

#### Gestión de Producción

- **Resource /api/products**: Productos
- **Resource /api/batches**: Lotes de producción
- **Resource /api/raw-materials**: Materias primas
- **Resource /api/inventory-movements**: Movimientos de inventario

#### Formularios y Respuestas

- **Resource /api/forms**: Formularios configurables
- **Resource /api/form-fields**: Campos de formularios
- **Resource /api/form-responses**: Respuestas a formularios
- **Resource /api/form-response-values**: Valores de respuestas

#### Otros Recursos

- **Resource /api/work-logs**: Registro de horas de trabajo
- **Resource /api/notifications**: Sistema de notificaciones

## Validación y Seguridad

El sistema utiliza Form Requests para validar todas las entradas, con mensajes de error personalizados en español:

- **UserRequest**: Validación de usuarios
- **ProductRequest**: Validación de productos
- **BatchRequest**: Validación de lotes
- **RawMaterialRequest**: Validación de materias primas
- **InventoryMovementRequest**: Validación de movimientos de inventario
- **FormRequest**: Validación de formularios
- **FormFieldRequest**: Validación de campos de formulario
- **FormResponseRequest**: Validación de respuestas
- **FormResponseValueRequest**: Validación de valores de respuestas
- **WorkLogRequest**: Validación de registros de trabajo
- **NotificationRequest**: Validación de notificaciones

## Solución de Problemas

### Problemas Comunes

1. **Fallo en la instalación de Composer**: Ejecute `composer update` primero debido a la compatibilidad con PHP 8.3
2. **Fallo en el servidor de desarrollo Vite en CI**: Añada `LARAVEL_BYPASS_ENV_CHECK=1` a .env
3. **Errores en endpoints de API**: Es posible que la base de datos necesite una configuración adecuada para las tablas User/Role
4. **Fallos en pruebas**: Asegúrese de que la base de datos esté migrada para el entorno de prueba
5. **Fallos de formato de código**: Ejecute `vendor/bin/pint` antes de realizar commits

### Notas de Rendimiento

- Las compilaciones del frontend son muy rápidas (~1.5 segundos)
- Las pruebas se ejecutan rápidamente (~0.5 segundos)
- La configuración inicial de composer es lenta (5-6 minutos) pero las instalaciones posteriores son rápidas
- Las operaciones de base de datos son rápidas debido a SQLite

## Advertencias Críticas

- **NUNCA CANCELE** el comando composer update - siempre permita 10+ minutos
- **SIEMPRE ejecute Pint** antes de realizar commits para evitar fallos en CI
- **Esquema de base de datos incompleto** - la funcionalidad completa requiere configuración adicional
- **Se requiere autenticación JWT** para la mayoría de los endpoints de API

## Estructura de Directorios

- **app/**: Contiene el código principal de la aplicación
  - **Http/Controllers/**: Controladores para cada recurso
  - **Http/Requests/**: Form Requests para validación
  - **Http/Middleware/**: Middleware personalizado (roles, sesión)
  - **Models/**: Modelos de Eloquent

- **config/**: Archivos de configuración
  - **auth.php**: Configuración de autenticación
  - **jwt.php**: Configuración de JWT

- **database/**: Migraciones y seeds
  - **migrations/**: Definición del esquema de base de datos

- **routes/**: Definición de rutas
  - **api.php**: Rutas de la API
  - **web.php**: Rutas web (no API)

## Contacto y Soporte

Para soporte técnico, comuníquese con el equipo de desarrollo en <support@toliboy.com> o abra un issue en el repositorio del proyecto.

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
