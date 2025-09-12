# Architecture Documentation - Gestión Toliboy

## Visión General del Sistema

Gestión Toliboy es un sistema web de gestión de producción construido con una arquitectura moderna basada en Laravel 12, que sigue el patrón Model-View-Controller (MVC) y principios de desarrollo REST API.

## Arquitectura General

```
┌─────────────────────────────────────────────────────────────┐
│                        Frontend                             │
│                  (Future Implementation)                   │
├─────────────────────────────────────────────────────────────┤
│                       API Layer                            │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────┐  │
│  │   Controllers   │  │   Middleware    │  │   Routes    │  │
│  │                 │  │                 │  │             │  │
│  │ AuthController  │  │ RoleAuth        │  │ api.php     │  │
│  │ UserController  │  │ JWTAuth         │  │ web.php     │  │
│  │ RoleController  │  │                 │  │             │  │
│  └─────────────────┘  └─────────────────┘  └─────────────┘  │
├─────────────────────────────────────────────────────────────┤
│                     Business Logic                         │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────┐  │
│  │    Services     │  │   Models        │  │ Validation  │  │
│  │                 │  │                 │  │             │  │
│  │ (Future)        │  │ User            │  │ Requests    │  │
│  │                 │  │ Product         │  │ Rules       │  │
│  │                 │  │ Batch           │  │             │  │
│  └─────────────────┘  └─────────────────┘  └─────────────┘  │
├─────────────────────────────────────────────────────────────┤
│                     Data Layer                             │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────┐  │
│  │   Database      │  │   Migrations    │  │   Seeders   │  │
│  │                 │  │                 │  │             │  │
│  │ MySQL/PostgreSQL│  │ Schema          │  │ Test Data   │  │
│  │                 │  │ Structure       │  │             │  │
│  └─────────────────┘  └─────────────────┘  └─────────────┘  │
└─────────────────────────────────────────────────────────────┘
```

## Capas de la Aplicación

### 1. Capa de Presentación (API)

#### Controllers (`app/Http/Controllers/`)
- **AuthController**: Manejo de autenticación y autorización
- **UserDataController**: CRUD de datos personales de usuarios
- **RoleController**: Gestión de roles y permisos

#### Middleware (`app/Http/Middleware/`)
- **RoleAuthorization**: Verificación de roles de usuario
- **JWTAuth**: Autenticación mediante tokens JWT

#### Routes (`routes/`)
- **api.php**: Definición de endpoints API
- **web.php**: Rutas web (futuro)

### 2. Capa de Lógica de Negocio

#### Models (`app/Models/`)
Los modelos Eloquent representan las entidades del dominio:

**Módulo de Usuarios:**
- `User`: Usuario del sistema
- `Role`: Roles y permisos
- `PersonalDatum`: Datos personales
- `ActiveSession`: Sesiones activas
- `CurrentUserSession`: Sesión actual del usuario

**Módulo de Producción:**
- `Product`: Productos manufacturados
- `Batch`: Lotes de producción
- `RawMaterial`: Materias primas
- `InventoryMovement`: Movimientos de inventario

**Módulo de Trabajo:**
- `WorkLog`: Registro de trabajo
- `UserWorkSummary`: Resumen de trabajo

**Módulo de Formularios:**
- `Form`: Formularios dinámicos
- `FormField`: Campos de formulario
- `FormResponse`: Respuestas a formularios
- `FormResponseDetail`: Detalles de respuestas
- `FormResponseValue`: Valores específicos

**Módulo de Sistema:**
- `Notification`: Notificaciones
- `AuditLog`: Log de auditoría
- `VCurrentStock`: Vista de stock actual

### 3. Capa de Datos

#### Database
- **Motor**: MySQL/PostgreSQL compatible
- **ORM**: Eloquent
- **Migraciones**: Control de versiones de esquema
- **Seeders**: Datos iniciales y de prueba

## Patrones de Diseño Implementados

### 1. MVC (Model-View-Controller)
- **Model**: Entidades de negocio (Eloquent Models)
- **View**: Respuestas JSON (API)
- **Controller**: Lógica de control y orquestación

### 2. Repository Pattern (Implícito con Eloquent)
- Los modelos Eloquent actúan como repositorios
- Abstracción de acceso a datos
- Métodos de consulta encapsulados

### 3. Middleware Pattern
- Filtros de requests HTTP
- Separación de concerns de autenticación/autorización
- Pipeline de procesamiento de requests

### 4. Factory Pattern (Laravel)
- Configuración de servicios
- Inyección de dependencias
- Service Container de Laravel

## Autenticación y Autorización

### JWT (JSON Web Tokens)
```
┌─────────────┐    ┌──────────────┐    ┌─────────────┐
│   Client    │───►│   Laravel    │───►│  Database   │
│             │    │              │    │             │
│ 1. Login    │    │ 2. Validate  │    │ 3. User     │
│ Credentials │    │ & Generate   │    │ Verification│
│             │    │ JWT Token    │    │             │
└─────────────┘    └──────────────┘    └─────────────┘
       ▲                    │
       │                    ▼
┌─────────────┐    ┌──────────────┐
│ 6. Protected│    │ 4. Return    │
│ Resources   │    │ JWT Token    │
│ Access      │    │              │
└─────────────┘    └──────────────┘
       ▲                    │
       │                    ▼
┌─────────────┐    ┌──────────────┐
│ 5. Validate │    │ Future       │
│ Token on    │    │ Requests     │
│ Each Request│    │ with Token   │
└─────────────┘    └──────────────┘
```

### Sistema de Roles
- **Developer**: Acceso completo al sistema
- **Admin**: Acceso administrativo (futuro)
- **User**: Acceso básico (futuro)

## Estructura de Directorios

```
gestión_toliboy/
├── app/                          # Código de la aplicación
│   ├── Http/                     # Capa HTTP
│   │   ├── Controllers/          # Controladores
│   │   ├── Middleware/           # Middleware personalizado
│   │   └── Kernel.php           # Kernel HTTP
│   ├── Models/                   # Modelos Eloquent
│   └── Providers/               # Proveedores de servicios
├── bootstrap/                    # Archivos de arranque
├── config/                       # Archivos de configuración
├── database/                     # Migraciones, seeders, factories
│   ├── migrations/              # Migraciones de base de datos
│   ├── seeders/                 # Datos iniciales
│   └── factories/               # Factories para testing
├── docs/                         # Documentación del proyecto
│   ├── API.md                   # Documentación de API
│   ├── DATABASE.md              # Schema de base de datos
│   └── ARCHITECTURE.md          # Este documento
├── public/                       # Punto de entrada web
├── resources/                    # Assets y views
├── routes/                       # Definición de rutas
│   ├── api.php                  # Rutas de API
│   ├── web.php                  # Rutas web
│   └── console.php              # Comandos de consola
├── storage/                      # Almacenamiento de la aplicación
├── tests/                        # Tests automatizados
├── vendor/                       # Dependencias de Composer
├── composer.json                 # Dependencias PHP
├── package.json                 # Dependencias Node.js
└── README.md                    # Documentación principal
```

## Flujo de Datos

### 1. Request Flow
```
HTTP Request → Routes → Middleware → Controller → Model → Database
                ↓
HTTP Response ← JSON ← Controller ← Model ← Database
```

### 2. Authentication Flow
```
1. User sends credentials → AuthController::login()
2. Validate credentials → User model
3. Generate JWT token → JWTAuth service
4. Return token → Client
5. Client includes token in headers → Middleware
6. Middleware validates token → JWTAuth service
7. Extract user from token → Continue to controller
```

### 3. Authorization Flow
```
1. Request with token → RoleAuthorization middleware
2. Extract user role from token → JWT payload
3. Check required roles → Middleware logic
4. Allow/Deny access → Continue/Return 403
```

## Tecnologías y Dependencias

### Core Framework
- **Laravel 12**: Framework PHP principal
- **PHP 8.2+**: Lenguaje base

### Authentication & Security
- **tymon/jwt-auth 2.2**: Autenticación JWT
- **Laravel Sanctum 4.0**: API token auth (backup)

### Database
- **Eloquent ORM**: Mapeado objeto-relacional
- **reliese/laravel 1.4**: Generación de modelos

### Development Tools
- **Laravel Pint 1.24**: Code styling
- **Laravel Sail 1.41**: Entorno Docker
- **Pest PHP 3.8**: Framework de testing

### Frontend Build Tools
- **Vite 7.0**: Asset bundling
- **Tailwind CSS 4.0**: CSS framework
- **Laravel Vite Plugin 2.0**: Integración Laravel-Vite

## Principios de Diseño

### 1. SOLID Principles
- **Single Responsibility**: Cada clase tiene una responsabilidad
- **Open/Closed**: Abierto para extensión, cerrado para modificación
- **Liskov Substitution**: Subtipos deben ser sustituibles
- **Interface Segregation**: Interfaces específicas
- **Dependency Inversion**: Dependencias de abstracciones

### 2. DRY (Don't Repeat Yourself)
- Reutilización de componentes
- Traits y helpers compartidos
- Configuración centralizada

### 3. Convention over Configuration
- Convenciones de Laravel
- Naming conventions consistentes
- Estructura estándar de directorios

## Escalabilidad y Performance

### 1. Database Optimization
- Índices en campos frecuentemente consultados
- Relaciones Eloquent optimizadas
- Query optimization mediante Eloquent

### 2. Caching Strategy (Future)
- Redis para cache de sesiones
- Cache de consultas frecuentes
- Response caching

### 3. API Rate Limiting (Future)
- Throttling por usuario
- Rate limiting por endpoint
- Protección contra abuse

## Security Considerations

### 1. Authentication
- JWT tokens con expiración
- Refresh token mechanism
- Secure password hashing (bcrypt)

### 2. Authorization
- Role-based access control
- Middleware protection
- API endpoint protection

### 3. Data Validation
- Input validation en controllers
- Eloquent model validation
- SQL injection prevention (Eloquent)

### 4. CORS Protection
- fruitcake/laravel-cors
- Configured allowed origins
- Secure headers

## Monitoring y Logging

### 1. Laravel Logging
- Monolog integration
- Multiple log channels
- Error tracking

### 2. Audit Trail
- AuditLog model
- User action tracking
- System event logging

## Deployment Architecture

### 1. Production Environment
```
Load Balancer
      │
      ▼
┌─────────────┐    ┌─────────────┐
│  App Server │    │  App Server │
│  (Laravel)  │    │  (Laravel)  │
└─────────────┘    └─────────────┘
      │                    │
      └────────┬───────────┘
               ▼
        ┌─────────────┐
        │  Database   │
        │ (MySQL/PG)  │
        └─────────────┘
```

### 2. Development Environment
- Laravel Sail (Docker)
- Local MySQL/PostgreSQL
- Vite development server

## Future Roadmap

### 1. Planned Features
- Frontend application (Vue.js/React)
- Real-time notifications (WebSockets)
- Advanced reporting system
- Mobile application

### 2. Technical Improvements
- Service layer implementation
- Event-driven architecture
- Advanced caching
- Microservices migration (future)

### 3. Infrastructure
- CI/CD pipeline
- Automated testing
- Performance monitoring
- Scalable deployment

## Testing Strategy

### 1. Unit Testing
- Model testing
- Service testing
- Utility function testing

### 2. Feature Testing
- API endpoint testing
- Authentication flow testing
- Business logic testing

### 3. Integration Testing
- Database integration
- External service integration
- End-to-end workflows

## Documentation Standards

### 1. Code Documentation
- PHPDoc comments
- Inline comments for complex logic
- README files for modules

### 2. API Documentation
- OpenAPI specification (future)
- Endpoint documentation
- Request/response examples

### 3. Database Documentation
- Schema documentation
- Relationship diagrams
- Migration documentation

---

*Esta documentación de arquitectura proporciona una visión completa del diseño y estructura del sistema Gestión Toliboy.*