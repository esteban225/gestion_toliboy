# Gestión Toliboy - Sistema de Gestión de Panadería y Pastelería

Sistema de gestión integral para empresas de producción panadera y pastelera construido con Laravel 12.0. Incluye gestión de lotes de producción, inventario, formularios dinámicos, control de calidad, logs de trabajo y reportes con autenticación JWT y control de acceso basado en roles.

## 🚀 Características Principales

- **Autenticación JWT** con control de acceso basado en roles (Developer, Gerente General, Ingenieros, Operarios, Trazabilidad)
- **API RESTful** completa con documentación
- **Formularios Dinámicos** para control de calidad y producción
- **Gestión de Inventario** con seguimiento de materias primas y productos
- **Generación de Reportes** en PDF/CSV/Excel
- **Sistema de Roles y Permisos** granular
- **Logs de Trabajo** y seguimiento de actividades
- **Dashboard** interactivo con métricas
- **Arquitectura Modular** limpia y escalable
- **Frontend** moderno con Vite y TailwindCSS

## 🏗️ Arquitectura del Sistema

### Estructura Modular
El proyecto utiliza una arquitectura modular basada en DDD (Domain Driven Design):

```
app/
├── Http/Controllers/          # Controladores principales
├── Models/                    # Modelos Eloquent
├── Modules/                   # Módulos del dominio
│   ├── Forms/                 # Gestión de formularios dinámicos
│   ├── Reports/              # Generación de reportes
│   ├── WorkLogs/             # Logs de trabajo
│   ├── Inventory/            # Gestión de inventario
│   ├── Users/                # Gestión de usuarios
│   ├── Notifications/        # Sistema de notificaciones
│   └── Roles/                # Control de acceso
└── Providers/                # Service Providers
```

### Roles del Sistema
- **DEV**: Desarrollador con acceso completo
- **GG**: Gerente General (dashboards, estadísticas)
- **INPL/INPR**: Ingenieros de Planta/Proceso (formularios, work-logs)
- **TRZ**: Trazabilidad (informes, lectura de formularios)
- **OP**: Operarios (diligenciamiento de formularios, registro de horas)

## 📋 Requisitos del Sistema

- **PHP**: >= 8.3
- **Composer**: >= 2.0
- **Node.js**: >= 16.x
- **NPM**: >= 8.x
- **Base de Datos**: MySQL/SQLite
- **Extensiones PHP**: OpenSSL, PDO, Mbstring, Tokenizer, XML, Ctype, JSON, BCMath, Fileinfo

## ⚡ Instalación y Configuración

### 1. Clonar el Repositorio
```bash
git clone <repository-url>
cd gestion_toliboy
```

### 2. Instalar Dependencias PHP (CRÍTICO)
```bash
# REQUERIDO para compatibilidad con PHP 8.3 - NO CANCELAR (5-6 minutos)
composer update --no-interaction

# Después de la actualización
composer install --no-interaction
```

### 3. Instalar Dependencias Node.js
```bash
npm install
npm audit fix
```

### 4. Configuración del Entorno
```bash
# Copiar archivo de configuración
cp .env.example .env

# Generar claves de aplicación
php artisan key:generate
php artisan jwt:secret
```

### 5. Configurar Base de Datos
```bash
# Para SQLite (desarrollo)
touch database/database.sqlite

# Para MySQL, configurar en .env:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=gestion_toliboy
# DB_USERNAME=root
# DB_PASSWORD=
```

### 6. Ejecutar Migraciones
```bash
# Crear tabla de sesiones (requerida para interfaz web)
php artisan session:table

# Ejecutar migraciones
php artisan migrate
```

### 7. Construir Assets Frontend
```bash
npm run build
```

## 🚀 Ejecución del Sistema

### Servidor de Desarrollo
```bash
# Servidor web
php artisan serve --host=0.0.0.0 --port=8000

# Modo desarrollo con hot reload
LARAVEL_BYPASS_ENV_CHECK=1 composer run dev
```

### Acceso al Sistema
- **Aplicación Web**: http://localhost:8000
- **API Base**: http://localhost:8000/api
- **Documentación API**: http://localhost:8000/docs/api

## 🗄️ Base de Datos

### Esquema Principal
```sql
-- Usuarios y Roles
users, roles, user_roles

-- Inventario
raw_materials, products, batches, inventory_movements

-- Formularios Dinámicos
forms, form_fields, form_responses, form_response_values

-- Trabajo y Trazabilidad
work_logs, notifications

-- Auditoría
audit_logs
```

### Vistas del Sistema
- `v_users_by_role` - Usuarios por rol
- `v_products_by_category` - Productos por categoría
- `v_batches_by_status` - Lotes por estado
- `v_inventory_monthly_summary` - Resumen mensual de inventario
- `v_forms_completion_rate` - Tasa de completitud de formularios
- `v_user_work_hours_by_month` - Horas trabajadas por usuario
- `v_current_stock` - Stock actual
- Y más vistas especializadas...

## 🔐 API y Autenticación

### Endpoints Principales
```bash
# Autenticación
POST /api/register
POST /api/login
POST /api/logout
POST /api/refresh

# Recursos (requieren autenticación JWT)
GET|POST|PUT|DELETE /api/forms
GET|POST|PUT|DELETE /api/form-responses
GET|POST|PUT|DELETE /api/work-logs
GET|POST|PUT|DELETE /api/products
GET|POST|PUT|DELETE /api/batches
GET|POST|PUT|DELETE /api/inventory-movements

# Reportes
GET /api/forms/{formId}/report/pdf
GET /api/reports/{reportName}
GET /api/reports/{reportName}/export
```

### Autenticación JWT
```bash
# Login y obtener token
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","password":"password"}'

# Usar token en requests
curl -H "Authorization: Bearer <TOKEN>" \
  http://localhost:8000/api/forms
```

## 📊 Sistema de Formularios Dinámicos

### Creación de Formularios
Los formularios se crean dinámicamente con campos configurables:
- Texto, textarea, select, checkbox, number, date, file
- Validaciones personalizadas
- Opciones múltiples para campos select/checkbox

### Generación de Reportes
```php
// Generar PDF de formulario
GET /api/forms/{formId}/report/pdf?date_from=2024-01-01&date_to=2024-12-31

// Parámetros disponibles:
// - format: csv, pdf, xlsx
// - date_from, date_to: filtros de fecha
// - limit: límite de registros (máximo 5000)
```

## 🧪 Testing y Calidad

### Ejecutar Tests
```bash
# Tests completos
php artisan test

# Tests específicos
php artisan test tests/Feature/FormsTest.php
php artisan test --filter test_create_form
```

### Formateo de Código
```bash
# Verificar formato
vendor/bin/pint --test

# Aplicar formato (EJECUTAR ANTES DE COMMIT)
vendor/bin/pint
```

## 📁 Estructura de Módulos

### Módulo Forms (Ejemplo)
```
app/Modules/Forms/
├── Domain/
│   ├── Entities/          # Entidades del dominio
│   ├── Repositories/      # Interfaces de repositorio
│   └── Services/          # Servicios del dominio
├── Infrastructure/
│   └── Repositories/      # Implementaciones de repositorio
├── Application/
│   └── UseCases/          # Casos de uso
└── Http/
    ├── Controllers/       # Controladores HTTP
    ├── Requests/          # Form Requests
    └── routes.php         # Rutas del módulo
```

## 🔧 Configuración Avanzada

### Variables de Entorno Importantes
```bash
# Aplicación
APP_NAME="Gestión Toliboy"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Base de Datos
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=gestion_toliboy

# JWT
JWT_SECRET=your-jwt-secret-key
JWT_TTL=60

# Para desarrollo con Vite en CI
LARAVEL_BYPASS_ENV_CHECK=1
```

### Configuración de Roles
```php
// Configurar roles por defecto
php artisan db:seed --class=RolesSeeder
```

## 🚨 Solución de Problemas

### Problemas Comunes

1. **Error de Composer**
   ```bash
   # SOLUCIÓN: Actualizar dependencias primero
   composer update --no-interaction
   ```

2. **Error CSRF en API**
   ```php
   // Excluir rutas API en app/Http/Middleware/VerifyCsrfToken.php
   protected $except = ['api/*'];
   ```

3. **Error de Vite en CI**
   ```bash
   # Agregar a .env
   LARAVEL_BYPASS_ENV_CHECK=1
   ```

4. **Problemas con Reportes PDF**
   ```bash
   # Instalar dependencias de PDF
   composer require dompdf/dompdf
   ```

### Comandos de Limpieza
```bash
php artisan optimize:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
composer dump-autoload
```

## 📈 Rendimiento

### Tiempos de Build
- **Composer update**: 5-6 minutos (inicial)
- **NPM install**: ~10 segundos
- **Frontend build**: ~1.5 segundos
- **Tests**: ~0.5 segundos
- **Formateo código**: ~5 segundos
- **Migraciones**: ~0.2 segundos

### Optimizaciones
```bash
# Cache de configuración para producción
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimización de Composer
composer install --optimize-autoloader --no-dev
```

## 📚 Documentación Adicional

### Recursos Útiles
- [Documentación Laravel](https://laravel.com/docs)
- [JWT Auth Package](https://github.com/tymondesigns/jwt-auth)
- [Pest Testing](https://pestphp.com/)
- [Laravel Pint](https://laravel.com/docs/pint)

### Contribución
1. Fork el repositorio
2. Crear rama feature (`git checkout -b feature/nueva-funcionalidad`)
3. Commit cambios (`git commit -am 'Agregar nueva funcionalidad'`)
4. **Ejecutar Pint**: `vendor/bin/pint`
5. **Ejecutar tests**: `php artisan test`
6. Push a la rama (`git push origin feature/nueva-funcionalidad`)
7. Crear Pull Request

## ⚠️ Advertencias Críticas

- **NUNCA CANCELAR** `composer update` - permitir 10+ minutos
- **SIEMPRE ejecutar Pint** antes de commit para evitar fallos de CI
- **Esquema de BD incompleto** - funcionalidad completa requiere configuración adicional
- **Autenticación JWT requerida** para la mayoría de endpoints de API
- **Timeouts altos** para comandos de Composer en CI/CD

## 📄 Licencia

Este proyecto está licenciado bajo [MIT License](LICENSE).

---

**Contacto**: Para soporte y consultas, contactar al equipo de desarrollo.
