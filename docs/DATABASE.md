# Database Schema - Gestión Toliboy

## Diagrama de Relaciones (ERD)

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│     ROLES       │    │     USERS       │    │ PERSONAL_DATA   │
├─────────────────┤    ├─────────────────┤    ├─────────────────┤
│ id (PK)         │◄──┤ id (PK)         │───►│ id (PK)         │
│ name            │    │ name            │    │ user_id (FK)    │
│ description     │    │ email           │    │ num_phone       │
│ permissions     │    │ password        │    │ num_phone_alt   │
│ is_active       │    │ role_id (FK)    │    │ num_identification│
│ created_at      │    │ position        │    │ identification_type│
│ updated_at      │    │ is_active       │    │ address         │
└─────────────────┘    │ last_login      │    │ emergency_contact│
                       │ created_at      │    │ emergency_phone │
                       │ updated_at      │    │ created_at      │
                       └─────────────────┘    │ updated_at      │
                                              └─────────────────┘

┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│    PRODUCTS     │    │     BATCHES     │    │   WORK_LOGS     │
├─────────────────┤    ├─────────────────┤    ├─────────────────┤
│ id (PK)         │◄──┤ id (PK)         │    │ id (PK)         │
│ name            │    │ name            │    │ user_id (FK)    │
│ code            │    │ code            │    │ date            │
│ category        │    │ product_id (FK) │    │ start_time      │
│ description     │    │ start_date      │◄──┤ end_time        │
│ specifications  │    │ expected_end_date│   │ total_hours     │
│ unit_price      │    │ actual_end_date │    │ overtime_hours  │
│ is_active       │    │ status          │    │ batch_id (FK)   │
│ created_by (FK) │    │ quantity        │    │ task_description│
│ created_at      │    │ defect_quantity │    │ notes           │
│ updated_at      │    │ notes           │    │ created_at      │
└─────────────────┘    │ created_by (FK) │    │ updated_at      │
                       │ created_at      │    └─────────────────┘
                       │ updated_at      │
                       └─────────────────┘

┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   RAW_MATERIALS │    │INVENTORY_MOVEMENTS│   │     FORMS       │
├─────────────────┤    ├─────────────────┤    ├─────────────────┤
│ id (PK)         │    │ id (PK)         │    │ id (PK)         │
│ name            │    │ movement_type   │    │ name            │
│ code            │    │ quantity        │    │ code            │
│ unit_measure    │    │ date            │    │ description     │
│ current_stock   │    │ raw_material_id │◄──┤ version         │
│ minimum_stock   │    │ batch_id (FK)   │    │ created_by (FK) │
│ supplier        │    │ notes           │    │ is_active       │
│ is_active       │    │ created_by (FK) │    │ display_order   │
│ created_by (FK) │    │ created_at      │    │ created_at      │
│ created_at      │    │ updated_at      │    │ updated_at      │
│ updated_at      │    └─────────────────┘    └─────────────────┘
└─────────────────┘
```

## Tablas Principales

### users
Tabla central de usuarios del sistema.

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | BIGINT AUTO_INCREMENT | Clave primaria |
| name | VARCHAR(255) | Nombre completo del usuario |
| email | VARCHAR(255) UNIQUE | Correo electrónico único |
| password | VARCHAR(255) | Contraseña hasheada |
| role_id | BIGINT NULL | FK a tabla roles |
| position | VARCHAR(255) NULL | Cargo/posición en la empresa |
| is_active | BOOLEAN DEFAULT 1 | Estado activo del usuario |
| last_login | TIMESTAMP NULL | Último inicio de sesión |
| created_at | TIMESTAMP | Fecha de creación |
| updated_at | TIMESTAMP | Fecha de última actualización |

**Relaciones:**
- `belongsTo(Role)`
- `hasOne(PersonalDatum)`
- `hasMany(WorkLog, Batch, Product, RawMaterial, Form, etc.)`

### roles
Sistema de roles y permisos.

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | BIGINT AUTO_INCREMENT | Clave primaria |
| name | VARCHAR(255) | Nombre del rol |
| description | TEXT NULL | Descripción del rol |
| permissions | JSON NULL | Permisos específicos del rol |
| is_active | BOOLEAN DEFAULT 1 | Estado activo del rol |
| created_at | TIMESTAMP | Fecha de creación |
| updated_at | TIMESTAMP | Fecha de última actualización |

**Relaciones:**
- `hasMany(User)`

### personal_data
Información personal detallada de los usuarios.

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | BIGINT AUTO_INCREMENT | Clave primaria |
| user_id | BIGINT UNIQUE | FK a tabla users |
| num_phone | VARCHAR(20) NULL | Número de teléfono principal |
| num_phone_alt | VARCHAR(20) NULL | Número de teléfono alternativo |
| num_identification | VARCHAR(50) NULL UNIQUE | Número de identificación |
| identification_type | VARCHAR(45) NULL | Tipo de identificación |
| address | VARCHAR(45) NULL | Dirección de residencia |
| emergency_contact | VARCHAR(100) NULL | Contacto de emergencia |
| emergency_phone | VARCHAR(25) NULL | Teléfono de emergencia |
| created_at | TIMESTAMP | Fecha de creación |
| updated_at | TIMESTAMP | Fecha de última actualización |

**Relaciones:**
- `belongsTo(User)`

### products
Catálogo de productos manufacturados.

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | BIGINT AUTO_INCREMENT | Clave primaria |
| name | VARCHAR(255) | Nombre del producto |
| code | VARCHAR(255) | Código único del producto |
| category | VARCHAR(255) NULL | Categoría del producto |
| description | TEXT NULL | Descripción detallada |
| specifications | JSON NULL | Especificaciones técnicas |
| unit_price | DECIMAL(10,2) NULL | Precio unitario |
| is_active | BOOLEAN DEFAULT 1 | Estado activo del producto |
| created_by | BIGINT NULL | FK a usuario creador |
| created_at | TIMESTAMP | Fecha de creación |
| updated_at | TIMESTAMP | Fecha de última actualización |

**Relaciones:**
- `belongsTo(User, 'created_by')`
- `hasMany(Batch)`

### batches
Lotes de producción.

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | BIGINT AUTO_INCREMENT | Clave primaria |
| name | VARCHAR(255) | Nombre del lote |
| code | VARCHAR(255) | Código único del lote |
| product_id | BIGINT NULL | FK a producto |
| start_date | DATETIME | Fecha de inicio de producción |
| expected_end_date | DATETIME NULL | Fecha esperada de finalización |
| actual_end_date | DATETIME NULL | Fecha real de finalización |
| status | VARCHAR(255) | Estado del lote |
| quantity | INT | Cantidad a producir |
| defect_quantity | INT NULL | Cantidad de productos defectuosos |
| notes | TEXT NULL | Notas adicionales |
| created_by | BIGINT NULL | FK a usuario creador |
| created_at | TIMESTAMP | Fecha de creación |
| updated_at | TIMESTAMP | Fecha de última actualización |

**Estados posibles:** `planned`, `in_progress`, `completed`, `cancelled`, `on_hold`

**Relaciones:**
- `belongsTo(Product)`
- `belongsTo(User, 'created_by')`
- `hasMany(WorkLog, FormResponse, InventoryMovement)`

### work_logs
Registro de horas de trabajo por usuario.

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | BIGINT AUTO_INCREMENT | Clave primaria |
| user_id | BIGINT | FK a usuario |
| date | DATE | Fecha de trabajo |
| start_time | TIME NULL | Hora de inicio |
| end_time | TIME NULL | Hora de finalización |
| total_hours | DECIMAL(5,2) NULL | Total de horas trabajadas |
| overtime_hours | DECIMAL(5,2) NULL | Horas extras |
| batch_id | BIGINT NULL | FK a lote relacionado |
| task_description | TEXT NULL | Descripción de la tarea |
| notes | TEXT NULL | Notas adicionales |
| created_at | TIMESTAMP | Fecha de creación |
| updated_at | TIMESTAMP | Fecha de última actualización |

**Relaciones:**
- `belongsTo(User)`
- `belongsTo(Batch)`

### raw_materials
Materias primas para la producción.

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | BIGINT AUTO_INCREMENT | Clave primaria |
| name | VARCHAR(255) | Nombre de la materia prima |
| code | VARCHAR(255) | Código único |
| unit_measure | VARCHAR(255) | Unidad de medida |
| current_stock | DECIMAL(10,2) | Stock actual |
| minimum_stock | DECIMAL(10,2) | Stock mínimo |
| supplier | VARCHAR(255) NULL | Proveedor |
| is_active | BOOLEAN DEFAULT 1 | Estado activo |
| created_by | BIGINT NULL | FK a usuario creador |
| created_at | TIMESTAMP | Fecha de creación |
| updated_at | TIMESTAMP | Fecha de última actualización |

**Relaciones:**
- `belongsTo(User, 'created_by')`
- `hasMany(InventoryMovement)`

### inventory_movements
Movimientos de inventario de materias primas.

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | BIGINT AUTO_INCREMENT | Clave primaria |
| movement_type | ENUM | Tipo de movimiento |
| quantity | DECIMAL(10,2) | Cantidad del movimiento |
| date | DATETIME | Fecha del movimiento |
| raw_material_id | BIGINT NULL | FK a materia prima |
| batch_id | BIGINT NULL | FK a lote relacionado |
| notes | TEXT NULL | Notas del movimiento |
| created_by | BIGINT NULL | FK a usuario que realizó el movimiento |
| created_at | TIMESTAMP | Fecha de creación |
| updated_at | TIMESTAMP | Fecha de última actualización |

**Tipos de movimiento:** `in`, `out`, `adjustment`, `transfer`

**Relaciones:**
- `belongsTo(RawMaterial)`
- `belongsTo(Batch)`
- `belongsTo(User, 'created_by')`

### forms
Formularios dinámicos del sistema.

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | BIGINT AUTO_INCREMENT | Clave primaria |
| name | VARCHAR(255) | Nombre del formulario |
| code | VARCHAR(255) | Código único |
| description | TEXT NULL | Descripción |
| version | VARCHAR(255) | Versión del formulario |
| created_by | BIGINT NULL | FK a usuario creador |
| is_active | BOOLEAN DEFAULT 1 | Estado activo |
| display_order | INT | Orden de visualización |
| created_at | TIMESTAMP | Fecha de creación |
| updated_at | TIMESTAMP | Fecha de última actualización |

**Relaciones:**
- `belongsTo(User, 'created_by')`
- `hasMany(FormField, FormResponse)`

## Tablas de Soporte

### notifications
Sistema de notificaciones.

### audit_logs
Log de auditoría de acciones del sistema.

### active_sessions
Control de sesiones activas de usuarios.

### form_fields
Campos de los formularios dinámicos.

### form_responses
Respuestas a los formularios.

### form_response_details
Detalles de las respuestas.

### form_response_values
Valores específicos de las respuestas.

### user_work_summary
Resumen de trabajo por usuario.

### v_current_stock
Vista para consulta rápida de stock actual.

## Índices Recomendados

```sql
-- Índices para optimizar consultas frecuentes
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_role_id ON users(role_id);
CREATE INDEX idx_personal_data_user_id ON personal_data(user_id);
CREATE INDEX idx_batches_product_id ON batches(product_id);
CREATE INDEX idx_batches_status ON batches(status);
CREATE INDEX idx_work_logs_user_date ON work_logs(user_id, date);
CREATE INDEX idx_work_logs_batch_id ON work_logs(batch_id);
CREATE INDEX idx_inventory_movements_date ON inventory_movements(date);
CREATE INDEX idx_inventory_movements_raw_material ON inventory_movements(raw_material_id);
```

## Consideraciones de Diseño

1. **Normalización**: La base de datos está normalizada para evitar redundancia de datos.

2. **Integridad Referencial**: Se utilizan claves foráneas para mantener la consistencia.

3. **Auditabilidad**: Todas las tablas principales incluyen campos `created_at` y `updated_at`.

4. **Flexibilidad**: Uso de campos JSON para datos variables como `specifications` y `permissions`.

5. **Performance**: Índices estratégicos para optimizar consultas frecuentes.

6. **Escalabilidad**: Diseño preparado para crecimiento de datos y usuarios.