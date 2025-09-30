# Gestión Toliboy - Sistema de Gestión de Panadería y Pastelería

Sistema de gestión integral para empresas de producción panadera y pastelera construido con **Laravel 12.0** y arquitectura **Domain Driven Design (DDD)**. Incluye gestión de lotes de producción, inventario, formularios dinámicos, control de calidad, logs de trabajo, sistema de notificaciones automáticas y reportes avanzados con autenticación JWT y control de acceso basado en roles.

## 🚀 Características Principales

- **Autenticación JWT** con control de acceso basado en roles (Developer, Gerente General, Ingenieros, Operarios, Trazabilidad)
- **API RESTful** completa con documentación automática (Scramble)
- **Formularios Dinámicos** para control de calidad y producción
- **Gestión de Inventario** con seguimiento automatizado y alertas de stock bajo
- **Sistema de WorkLogs** con registro automático de entrada/salida y detección de horas extra
- **Notificaciones Automáticas** con eventos y listeners para ausencias y alertas de inventario
- **Scheduler de Tareas** para automatización diaria de verificaciones
- **Generación de Reportes** en PDF/CSV/Excel con datos personalizados
- **Sistema de Roles y Permisos** granular con middleware especializado
- **Dashboard** interactivo con métricas en tiempo real
- **Arquitectura Modular DDD** limpia, escalable y mantenible
- **Frontend** moderno con Vite y TailwindCSS
- **Sistema de Colas** para procesamiento asíncrono de tareas

## 🏗️ Arquitectura del Sistema

### Estructura Modular DDD
El proyecto utiliza una arquitectura modular basada en **Domain Driven Design (DDD)** con separación clara de responsabilidades:

```text
app/
├── Http/Controllers/          # Controladores principales de Laravel
├── Models/                    # Modelos Eloquent compartidos
├── Console/Commands/          # Comandos Artisan personalizados
├── Jobs/                      # Trabajos de cola (Queue Jobs)
├── Observers/                 # Observadores de modelos
├── Providers/                 # Service Providers
└── Modules/                   # Módulos del dominio (DDD)
    ├── Auth/                  # Autenticación y autorización
    │   ├── Domain/
    │   ├── Infrastructure/
    │   ├── Application/
    │   └── Http/
    ├── WorkLogs/             # Registros de trabajo y asistencia
    │   ├── Domain/
    │   │   ├── Entities/      # WorkLogEntity
    │   │   ├── Repositories/  # WorkLogRepositoryI
    │   │   ├── Services/      # WorkLogService, WorkLogAbsenceService
    │   │   └── Events/        # UserAbsenceDetected, UserOvertimeDetected
    │   ├── Infrastructure/
    │   │   └── Repositories/  # WorkLogRepositoryE (Eloquent)
    │   ├── Application/
    │   │   ├── UseCases/      # WorkLogUseCase, RegisterWorkLogUseCase
    │   │   ├── DTOs/          # WorkLogDTO
    │   │   └── Listeners/     # SendAbsenceNotification, SendUserOvertimeNotifications
    │   └── Http/
    │       ├── Controllers/   # WorkLogController
    │       ├── Requests/      # WorkLogRegisterRequest, WorkLogUpDateRequest
    │       └── routes.php     # Rutas del módulo
    ├── Notifications/        # Sistema de notificaciones
    │   ├── Domain/
    │   │   ├── Entities/      # NotificationEntity
    │   │   ├── Repositories/  # NotificationRepositoryI
    │   │   └── Services/      # NotificationService
    │   ├── Infrastructure/
    │   │   └── Repositories/  # NotificationRepositoryE
    │   ├── Application/
    │   │   ├── UseCases/      # NotificationUseCase
    │   │   └── Listeners/     # SendLowStockNotification
    │   └── Http/
    │       ├── Controllers/   # NotificationController
    │       ├── Requests/      # RegisterRequest, UpdateRequest
    │       └── Resources/     # NotificationResource
    ├── Forms/                # Formularios dinámicos
    │   ├── Domain/
    │   ├── Infrastructure/
    │   ├── Application/
    │   └── Http/
    ├── Reports/              # Generación de reportes avanzados
    │   ├── Domain/
    │   │   └── Services/      # ReportExportService, ReportAggregatorService
    │   ├── Infrastructure/
    │   │   └── Repositories/  # ReportsRepository
    │   ├── Application/
    │   │   └── UseCases/      # GenerateReportUseCase
    │   └── Http/
    ├── Inventory/            # Gestión de inventario
    │   ├── RawMaterials/     # Materias primas
    │   ├── Batches/          # Lotes de producción
    │   └── InventoryMovements/ # Movimientos de inventario
    ├── Users/                # Gestión de usuarios
    └── Roles/                # Control de acceso y roles
```

### Eventos y Listeners
El sistema implementa un robusto patrón de eventos para notificaciones automáticas:

- **UserAbsenceDetected**: Se dispara cuando un usuario no registra asistencia
- **UserOvertimeDetected**: Se dispara cuando se detectan horas extra excesivas
- **InventoryLowStock**: Se dispara cuando el stock está por debajo del mínimo

### Comandos Artisan Automatizados
- `worklogs:notify-absences`: Verifica diariamente ausencias de usuarios
- `worklogs:send-business-day`: Envía notificaciones de días laborales

### Roles del Sistema
- **DEV**: Desarrollador con acceso completo al sistema
- **GG**: Gerente General (dashboards, estadísticas, reportes ejecutivos)
- **INGPL/INGPR**: Ingenieros de Planta/Proceso (formularios, work-logs, supervisión)
- **TRZ**: Trazabilidad (informes, lectura de formularios, auditoría)
- **OP**: Operarios (diligenciamiento de formularios, registro de horas)

## � Sistema de Notificaciones Automáticas

### Arquitectura de Eventos y Listeners
El sistema implementa un patrón de **Events/Listeners** robusto para notificaciones automáticas en tiempo real:

#### Eventos Disponibles
```php
// Evento: Ausencia de usuario detectada
UserAbsenceDetected::class => [
    SendAbsenceNotification::class
]

// Evento: Horas extra excesivas detectadas  
UserOvertimeDetected::class => [
    SendUserOvertimeNotifications::class
]

// Evento: Stock bajo en inventario
InventoryLowStock::class => [
    SendLowStockNotification::class
]
```

#### Servicios de Notificación
- **NotificationService**: Servicio principal para crear y gestionar notificaciones
- **WorkLogAbsenceService**: Detecta ausencias y horas extra de usuarios
- **CheckLowStockJob**: Job de cola para verificar stock bajo

#### Automatización con Scheduler
Las verificaciones se ejecutan automáticamente mediante el scheduler de Laravel:

```php
// Programación diaria de verificaciones
Schedule::command('worklogs:notify-absences')->dailyAt('08:00');
Schedule::command('worklogs:send-business-day')->weekdays('monday')->at('09:00');
```

#### Comandos Artisan
- **`worklogs:notify-absences`**: Verifica ausencias diarias de usuarios
- **`worklogs:send-business-day`**: Procesa notificaciones de días laborales

#### Flujo de Notificaciones

```mermaid
graph TD
    A[Scheduler Diario 08:00] --> B[Comando: worklogs:notify-absences]
    B --> C[WorkLogAbsenceService]
    C --> D{¿Usuario sin WorkLog?}
    D -->|Sí| E[Disparar UserAbsenceDetected]
    D -->|No| F[Verificar Horas Extra]
    E --> G[SendAbsenceNotification Listener]
    F --> H{¿Horas > 24?}
    H -->|Sí| I[Disparar UserOvertimeDetected]
    I --> J[SendUserOvertimeNotifications Listener]
    G --> K[NotificationService]
    J --> K
    K --> L[Crear Notificación en DB]
    L --> M[Notificación Visible para Usuarios]
```

### Tipos de Notificaciones
- **info**: Información general del sistema
- **warning**: Alertas de advertencia (stock bajo, ausencias)
- **error**: Errores críticos del sistema
- **success**: Confirmaciones de acciones exitosas


## 📋 Requisitos del Sistema

### Tecnologías Base
- **PHP**: >= 8.2 (compatible con 8.3)
- **Laravel**: 12.0 (Framework principal)
- **Composer**: >= 2.0 (Gestión de dependencias PHP)
- **Node.js**: >= 16.x (Build tools y frontend)
- **NPM**: >= 8.x (Gestión de dependencias JS)

### Base de Datos
- **MySQL**: >= 8.0 (Recomendado para producción)
- **SQLite**: Disponible para desarrollo
- **PostgreSQL**: Compatible (configuración manual)

### Dependencias PHP Principales
```json
{
  "php": "^8.2",
  "laravel/framework": "^12.0",
  "tymon/jwt-auth": "^2.2",
  "dedoc/scramble": "^0.12.34",
  "dompdf/dompdf": "^3.1",
  "maatwebsite/excel": "^3.1",
  "laravel/sanctum": "^4.0",
  "laravel/tinker": "^2.10.1"
}
```

### Dependencias de Desarrollo
```json
{
  "pestphp/pest": "^3.8",
  "pestphp/pest-plugin-laravel": "^3.2",
  "laravel/pint": "^1.24",
  "laravel/pail": "^1.2.2",
  "reliese/laravel": "^1.4",
  "fakerphp/faker": "^1.23"
}
```

### Frontend y Build Tools
```json
{
  "vite": "^7.0.4",
  "laravel-vite-plugin": "^2.0.0",
  "@tailwindcss/vite": "^4.0.0",
  "tailwindcss": "^4.0.0",
  "axios": "^1.11.0",
  "concurrently": "^9.0.1"
}
```

### Extensiones PHP Requeridas
- **OpenSSL**: Para encriptación y JWT
- **PDO**: Conexiones de base de datos
- **Mbstring**: Manipulación de cadenas multibyte
- **Tokenizer**: Análisis de tokens PHP
- **XML**: Procesamiento de XML
- **Ctype**: Verificación de tipos de caracteres
- **JSON**: Manipulación de JSON
- **BCMath**: Matemáticas de precisión arbitraria
- **Fileinfo**: Información de archivos
- **GD**: Manipulación de imágenes (opcional para reportes)

### Herramientas de Calidad de Código
- **Laravel Pint**: Formateo automático de código PHP
- **Pest**: Framework de testing moderno
- **Scramble**: Generación automática de documentación API
- **Reliese Laravel**: Generación de modelos desde DB

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
El sistema proporciona una API RESTful completa con documentación automática:

#### Autenticación JWT
```bash
# Registro de usuario
POST /api/register

# Inicio de sesión
POST /api/login

# Cerrar sesión
POST /api/logout

# Refrescar token
POST /api/refresh
```

#### WorkLogs (Registros de Trabajo)
```bash
# Listar work logs con paginación y filtros
GET /api/work-logs?page=1&per_page=15&user_id=1&date=2024-01-01

# Obtener work log específico
GET /api/work-logs/{id}

# Crear work log manual
POST /api/work-logs

# Actualizar work log
PUT /api/work-logs/{id}

# Eliminar work log
DELETE /api/work-logs/{id}

# Registro automático de entrada/salida
POST /api/work-logs/register/{userId}

# Work logs de un usuario específico
GET /api/hoursLog/users/{userId}
```

#### Notificaciones
```bash
# Listar notificaciones del usuario autenticado
GET /api/notifications

# Crear notificación
POST /api/notifications

# Obtener notificación específica
GET /api/notifications/{id}

# Actualizar notificación
PUT /api/notifications/{id}

# Marcar como leída
PATCH /api/notifications/{id}/read

# Eliminar notificación
DELETE /api/notifications/{id}
```

#### Reportes Avanzados
```bash
# Generar reporte con datos personalizados
POST /api/reports/custom
{
    "title": "Reporte de Producción",
    "data": [...],
    "format": "pdf|csv|xlsx"
}

# Reporte de formulario en PDF
GET /api/forms/{formId}/report/pdf?date_from=2024-01-01&date_to=2024-12-31

# Exportar reporte específico
GET /api/reports/{reportName}/export?format=xlsx

# Reporte con vista Blade personalizada
POST /api/reports/blade
{
    "view": "reports.custom",
    "data": {...},
    "title": "Mi Reporte"
}
```

#### Inventario y Materias Primas
```bash
# Gestión de materias primas
GET|POST|PUT|DELETE /api/raw-materials

# Gestión de lotes de producción
GET|POST|PUT|DELETE /api/batches

# Movimientos de inventario
GET|POST|PUT|DELETE /api/inventory-movements

# Productos
GET|POST|PUT|DELETE /api/products
```

#### Formularios Dinámicos
```bash
# Gestión de formularios
GET|POST|PUT|DELETE /api/forms

# Respuestas de formularios
GET|POST|PUT|DELETE /api/form-responses

# Campos de formulario
GET|POST|PUT|DELETE /api/form-fields
```

### Autenticación y Autorización

#### Headers requeridos
```bash
# Token JWT en todas las requests autenticadas
Authorization: Bearer <JWT_TOKEN>

# Content-Type para requests POST/PUT
Content-Type: application/json
```

#### Middleware de Roles
```bash
# Acceso por roles específicos
middleware(['jwt.auth', 'role:DEV,GG,INGPL'])

# Roles disponibles:
# - DEV: Acceso completo
# - GG: Gerente General
# - INGPL: Ingeniero de Planta  
# - INGPR: Ingeniero de Proceso
# - TRZ: Trazabilidad
# - OP: Operario
```

#### Ejemplo de uso con cURL
```bash
# 1. Obtener token
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@toliboy.com","password":"password"}'

# 2. Usar token en requests
curl -H "Authorization: Bearer <TOKEN>" \
  http://localhost:8000/api/work-logs

# 3. Registrar entrada/salida automática
curl -X POST http://localhost:8000/api/work-logs/register/1 \
  -H "Authorization: Bearer <TOKEN>"
```

### Documentación API Automática
- **Scramble**: Documentación automática en `/docs/api`
- **Servers configurados**:
  - Local: `http://127.0.0.1:8000/api`
  - Staging: `https://staging.toliboy.com/api`
  - Producción: `https://toliboy.com/api`

## ⏰ Automatización y Programación de Tareas

### Comandos Artisan Disponibles

#### Comandos de WorkLogs
```bash
# Verificar ausencias diarias de usuarios
php artisan worklogs:notify-absences

# Procesar notificaciones de días laborales
php artisan worklogs:send-business-day
```

#### Comandos de Sistema
```bash
# Generar nuevo módulo DDD
./New-Module.ps1 ModuleName

# Limpiar caché de aplicación
php artisan optimize:clear

# Inspiración diaria
php artisan inspire
```

### Scheduler (Programación Automática)

El sistema utiliza el **Task Scheduler** de Laravel para automatizar verificaciones críticas:

#### Configuración en `routes/console.php`
```php
// Verificación diaria de ausencias a las 8:00 AM
Schedule::command('worklogs:notify-absences')->dailyAt('08:00');

// Notificaciones de días laborales (Lunes a las 9:00 AM)
Schedule::command('worklogs:send-business-day')->weekdays('monday')->at('09:00');

// Comando de inspiración cada minuto (desarrollo)
Schedule::command('inspire')->everyMinute();
```

#### Ejecución del Scheduler

**Desarrollo**:
```bash
# Ejecutar scheduler en modo desarrollo
php artisan schedule:work
```

**Producción** (Crontab):
```bash
# Agregar al crontab del servidor
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

### Sistema de Colas (Queue)

#### Configuración de Colas
```bash
# Configuración por defecto: database
QUEUE_CONNECTION=database

# Ejecutar worker de colas
php artisan queue:work

# Reiniciar workers
php artisan queue:restart

# Monitorear trabajos fallidos
php artisan queue:failed
```

#### Jobs Disponibles
- **CheckLowStockJob**: Verifica stock bajo en materias primas
- **Procesamiento asíncrono** de notificaciones y reportes

### Generación Automática de Módulos

#### Script PowerShell para nuevos módulos
```powershell
# Crear nuevo módulo con estructura DDD completa
.\New-Module.ps1 "NuevoModulo"

# Estructura generada automáticamente:
# app/Modules/NuevoModulo/
# ├── Application/UseCases/
# ├── Domain/Entities/
# ├── Domain/Repositories/
# ├── Domain/Services/
# ├── Http/Controllers/
# ├── Http/Requests/
# └── Infrastructure/Repositories/
```

### Monitoreo y Logs

#### Logs de aplicación
```bash
# Ver logs en tiempo real
php artisan pail

# Ubicación de logs
storage/logs/laravel.log
```

#### Canales de log configurados
- **single**: Log único en archivo
- **daily**: Logs diarios con rotación
- **slack**: Notificaciones críticas a Slack
- **stack**: Múltiples canales combinados

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

## 🏛️ Arquitectura Domain Driven Design (DDD)

### Principios DDD Implementados

#### Separación por Capas
```text
┌─────────────────────────────────────────┐
│             HTTP Layer                  │  ← Controllers, Requests, Routes
├─────────────────────────────────────────┤
│         Application Layer               │  ← UseCases, DTOs, Listeners
├─────────────────────────────────────────┤
│           Domain Layer                  │  ← Entities, Services, Events, Repositories (interfaces)
├─────────────────────────────────────────┤
│        Infrastructure Layer            │  ← Repositories (implementations), External Services
└─────────────────────────────────────────┘
```

#### Ejemplo: Módulo WorkLogs
```php
// Domain Entity (Reglas de negocio)
class WorkLogEntity 
{
    private int $user_id;
    private ?string $date;
    private ?string $start_time;
    private ?string $end_time;
    
    public function calculateTotalHours(): ?string
    {
        // Lógica de dominio para calcular horas
    }
}

// Domain Repository Interface (Contrato)
interface WorkLogRepositoryI 
{
    public function findByUserAndDate(int $userId, string $date): ?WorkLogEntity;
    public function create(WorkLogDTO $workLog): WorkLogEntity;
}

// Infrastructure Implementation (Persistencia)
class WorkLogRepositoryE implements WorkLogRepositoryI 
{
    public function findByUserAndDate(int $userId, string $date): ?WorkLogEntity
    {
        // Implementación con Eloquent
        $workLog = WorkLog::where('user_id', $userId)->where('date', $date)->first();
        return $workLog ? $this->mapToEntity($workLog) : null;
    }
}

// Application Use Case (Casos de uso)
class RegisterWorkLogUseCase 
{
    public function __construct(private WorkLogService $workLogService) {}
    
    public function execute(int $userId): WorkLogEntity
    {
        return $this->workLogService->registerWorkLog($userId);
    }
}

// Domain Service (Lógica de dominio compleja)
class WorkLogService 
{
    public function registerWorkLog(int $userId): WorkLogEntity
    {
        // Lógica de negocio: entrada vs salida
        $existingLog = $this->repository->findByUserAndDate($userId, date('Y-m-d'));
        
        if (!$existingLog) {
            // Crear entrada
        } else {
            // Actualizar salida y calcular horas
        }
    }
}
```

#### Inyección de Dependencias
```php
// Service Provider Configuration
$this->app->bind(WorkLogRepositoryI::class, WorkLogRepositoryE::class);
$this->app->bind(NotificationRepositoryI::class, NotificationRepositoryE::class);

// Constructor Injection en Controllers
public function __construct(
    private WorkLogUseCase $workLogUseCase,
    private RegisterWorkLogUseCase $registerWorkLogUseCase
) {}
```

#### Eventos de Dominio
```php
// Domain Event
class UserAbsenceDetected 
{
    public function __construct(
        public int $userId,
        public string $userName,
        public Carbon $date
    ) {}
}

// Event Listener
class SendAbsenceNotification 
{
    public function handle(UserAbsenceDetected $event): void
    {
        // Lógica de aplicación para notificar
    }
}
```

### Beneficios de la Arquitectura DDD

#### 1. **Mantenibilidad**
- Separación clara de responsabilidades
- Código fácil de entender y modificar
- Cambios aislados por capa

#### 2. **Testabilidad**
- Interfaces permiten mocking fácil
- Lógica de dominio independiente de framework
- Tests unitarios y de integración separados

#### 3. **Escalabilidad**
- Módulos independientes
- Fácil adición de nuevas funcionalidades
- Reutilización de componentes

#### 4. **Flexibilidad**
- Cambio de persistencia sin afectar dominio
- Intercambio de implementaciones
- Adaptación a nuevos requerimientos

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

## 📈 Rendimiento y Métricas

### Tiempos de Build y Operaciones
- **Composer update**: 5-6 minutos (inicial con dependencias)
- **Composer install**: ~30 segundos (con cache)
- **NPM install**: ~10 segundos
- **Frontend build (Vite)**: ~1.5 segundos
- **Tests (Pest)**: ~0.5 segundos (suite completa)
- **Formateo código (Pint)**: ~5 segundos
- **Migraciones**: ~0.2 segundos
- **Generación API docs**: ~2 segundos
- **Ejecución scheduler**: <1 segundo por comando

### Métricas del Sistema

#### Líneas de Código (aproximado)
- **Total**: ~15,000 líneas
- **PHP**: ~12,000 líneas
- **JavaScript/CSS**: ~2,000 líneas
- **Configuración**: ~1,000 líneas

#### Cobertura de Funcionalidades
- **Módulos DDD**: 8 módulos principales
- **Endpoints API**: 50+ endpoints documentados
- **Comandos Artisan**: 12 comandos personalizados
- **Events/Listeners**: 6 eventos de dominio
- **Jobs de Cola**: 4 trabajos asíncronos
- **Middlewares**: 8 middlewares personalizados

#### Performance de API
```bash
# Endpoints típicos (tiempo de respuesta)
GET /api/work-logs        # ~150ms (paginado)
POST /api/work-logs       # ~200ms (con validación)
GET /api/notifications    # ~100ms (filtrado por usuario)
POST /api/reports/custom  # ~500ms (generación PDF)
```

### Optimizaciones Implementadas

#### Backend (Laravel)
```bash
# Cache de configuración para producción
php artisan config:cache
php artisan route:cache  
php artisan view:cache
php artisan event:cache

# Optimización de Composer
composer install --optimize-autoloader --no-dev

# Optimización de base de datos
php artisan db:show     # Verificar configuración
php artisan model:show  # Verificar relaciones
```

#### Frontend (Vite + TailwindCSS)
```bash
# Build optimizado para producción
npm run build  # Tree-shaking automático
               # CSS purging
               # Asset compression
               # Bundle splitting
```

#### Caching Strategy
- **Config Cache**: Configuración de Laravel
- **Route Cache**: Rutas compiladas  
- **View Cache**: Templates Blade
- **OPcache**: Bytecode PHP (recomendado)
- **Query Cache**: MySQL query cache
- **Application Cache**: Cache de datos específicos

### Monitoreo y Debugging

#### Herramientas Disponibles
```bash
# Logs en tiempo real
php artisan pail

# Debugging de consultas
php artisan db:monitor

# Estado de colas
php artisan queue:monitor

# Información del sistema
php artisan about
```

#### Profiling
- **Debugbar**: Disponible en desarrollo
- **Telescope**: Opcional para profiling avanzado
- **Logs estructurados**: Laravel Log con contexto
- **Error tracking**: Integración con servicios externos

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
