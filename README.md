# Gestión Toliboy - Sistema de Gestión de Producción

## Descripción del Proyecto

Gestión Toliboy es un sistema completo de gestión de producción desarrollado en Laravel 12, diseñado para administrar procesos productivos, control de inventarios, gestión de personal y seguimiento de lotes de producción.

## Características Principales

### 🔐 Sistema de Autenticación y Autorización
- Autenticación JWT (JSON Web Tokens)
- Sistema de roles y permisos granular
- Middleware de autorización por roles
- Gestión de sesiones activas de usuarios

### 📦 Gestión de Producción
- **Productos**: Catálogo completo con especificaciones técnicas
- **Lotes de Producción**: Control de batches con fechas, cantidades y estados
- **Materias Primas**: Inventario y control de materiales
- **Movimientos de Inventario**: Trazabilidad completa de materiales

### 👥 Gestión de Personal
- **Usuarios**: Sistema completo de usuarios con roles
- **Datos Personales**: Información detallada del personal
- **Registro de Trabajo**: Control de horas trabajadas y tareas
- **Resumen de Trabajo**: Reportes de productividad

### 📋 Sistema de Formularios
- **Formularios Dinámicos**: Creación de formularios personalizables
- **Campos de Formulario**: Diferentes tipos de campos
- **Respuestas**: Captura y almacenamiento de respuestas
- **Reportes**: Análisis de datos capturados

### 🔔 Sistema de Notificaciones
- Notificaciones a usuarios
- Alertas del sistema
- Log de auditoría completo

## Estructura del Proyecto

### Arquitectura MVC

```
app/
├── Http/
│   ├── Controllers/          # Controladores de la aplicación
│   │   ├── AuthController.php
│   │   ├── UserDataController.php
│   │   └── RoleController.php
│   ├── Middleware/           # Middleware personalizado
│   │   └── RoleAuthorization.php
│   └── Kernel.php           # Kernel HTTP
├── Models/                  # Modelos Eloquent
│   ├── User.php
│   ├── Role.php
│   ├── Product.php
│   ├── Batch.php
│   ├── WorkLog.php
│   ├── Form.php
│   └── ...
└── Providers/              # Proveedores de servicios
```

### Estructura de Base de Datos

#### Tablas Principales
- **users**: Usuarios del sistema
- **roles**: Roles y permisos
- **personal_data**: Datos personales de usuarios
- **products**: Catálogo de productos
- **raw_materials**: Materias primas
- **batches**: Lotes de producción
- **inventory_movements**: Movimientos de inventario
- **work_logs**: Registro de trabajo
- **forms**: Formularios dinámicos
- **form_fields**: Campos de formularios
- **form_responses**: Respuestas a formularios
- **notifications**: Sistema de notificaciones
- **audit_logs**: Log de auditoría

## Tecnologías Utilizadas

### Backend
- **Laravel 12**: Framework PHP principal
- **PHP 8.2+**: Lenguaje de programación
- **JWT Auth**: Autenticación mediante tokens
- **Eloquent ORM**: Mapeado objeto-relacional

### Frontend
- **Vite**: Bundler de assets
- **Tailwind CSS 4.0**: Framework CSS
- **Laravel Vite Plugin**: Integración con Laravel

### Testing
- **Pest PHP**: Framework de testing
- **PHPUnit**: Testing unitario

### Herramientas de Desarrollo
- **Laravel Pint**: Code styling
- **Laravel Sail**: Entorno de desarrollo Docker
- **Composer**: Gestor de dependencias PHP
- **NPM**: Gestor de paquetes Node.js

## Instalación y Configuración

### Requisitos
- PHP 8.2 o superior
- Composer
- Node.js y NPM
- Base de datos MySQL/PostgreSQL/SQLite

### Pasos de Instalación

1. **Clonar el repositorio**
   ```bash
   git clone https://github.com/esteban225/gestion_toliboy.git
   cd gestion_toliboy
   ```

2. **Instalar dependencias PHP**
   ```bash
   composer install
   ```

3. **Instalar dependencias Node.js**
   ```bash
   npm install
   ```

4. **Configurar variables de entorno**
   ```bash
   cp .env.example .env
   php artisan key:generate
   php artisan jwt:secret
   ```

5. **Configurar base de datos**
   - Editar el archivo `.env` con los datos de conexión
   - Ejecutar migraciones:
   ```bash
   php artisan migrate
   ```

6. **Semillas de datos (opcional)**
   ```bash
   php artisan db:seed
   ```

7. **Compilar assets**
   ```bash
   npm run build
   ```

### Desarrollo

```bash
# Iniciar servidor de desarrollo
composer run dev

# O alternativamente
php artisan serve &
npm run dev &
php artisan queue:listen --tries=1 &
```

## API Endpoints

### Autenticación
- `POST /api/register` - Registro de usuarios
- `POST /api/login` - Inicio de sesión
- `POST /api/logout` - Cerrar sesión
- `POST /api/refresh` - Refrescar token
- `GET /api/me` - Obtener usuario actual

### Datos de Usuario (Requiere rol Developer)
- `GET /api/userData` - Listar datos personales
- `POST /api/userData` - Crear datos personales
- `GET /api/userData/{id}` - Obtener datos específicos
- `PUT /api/userData/{id}` - Actualizar datos
- `DELETE /api/userData/{id}` - Eliminar datos

### Roles y Permisos
El sistema utiliza middleware personalizado para controlar el acceso:
- **Developer**: Acceso completo al sistema
- **Admin**: Acceso administrativo (rutas futuras)
- **User**: Acceso básico (rutas públicas)

## Testing

```bash
# Ejecutar todos los tests
composer run test

# O usando Pest directamente
./vendor/bin/pest

# Tests con coverage
./vendor/bin/pest --coverage
```

## Estructura de Modelos y Relaciones

### Modelo User
- **Relaciones**:
  - `hasOne(PersonalDatum)`
  - `belongsTo(Role)`
  - `hasMany(WorkLog, Batch, Product, etc.)`

### Modelo Product
- **Campos**: name, code, category, specifications (JSON)
- **Relaciones**: `hasMany(Batch)`, `belongsTo(User)`

### Modelo Batch
- **Campos**: name, code, start_date, end_date, status, quantity
- **Relaciones**: `belongsTo(Product, User)`, `hasMany(WorkLog, FormResponse)`

### Modelo WorkLog
- **Campos**: date, start_time, end_time, total_hours, task_description
- **Relaciones**: `belongsTo(User, Batch)`

## Contribución

1. Fork el proyecto
2. Crear una rama feature (`git checkout -b feature/AmazingFeature`)
3. Commit los cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abrir un Pull Request

## Licencia

Este proyecto está licenciado bajo la Licencia MIT. Ver el archivo `LICENSE` para más detalles.

## Contacto

**Desarrollador**: esteban225  
**GitHub**: [https://github.com/esteban225/gestion_toliboy](https://github.com/esteban225/gestion_toliboy)

---

*Sistema desarrollado para la gestión eficiente de procesos productivos y control de personal.*
