# Documentación Completa de Base de Datos - Toliboy Estable V2

## Índice de Contenidos
1. [Roles](#1-roles)
2. [Users](#2-users)
3. [Personal Data](#3-personal-data)
4. [Raw Materials](#4-raw-materials)
5. [Products](#5-products)
6. [Batches](#6-batches)
7. [Inventory Movements](#7-inventory-movements)
8. [Forms](#8-forms)
9. [Form Fields](#9-form-fields)
10. [Form Responses](#10-form-responses)
11. [Form Response Values](#11-form-response-values)
12. [Work Logs](#12-work-logs)
13. [Notifications](#13-notifications)
14. [Relaciones y Dependencias](#relaciones-y-dependencias)
14. [Orden de inserción](#-Orden-de-Inserción-de-Datos)

---

## 1. Roles

**Propósito:** Define los roles de usuario en el sistema con sus permisos correspondientes.

### Estructura de Campos:
- `id` (BIGINT, AUTO_INCREMENT, PRIMARY KEY): Identificador único
- `name` (VARCHAR(50), NOT NULL, UNIQUE): Nombre del rol
- `description` (TEXT, NULL): Descripción del rol
- `permissions` (JSON, NULL): Permisos en formato JSON
- `is_active` (TINYINT(1), DEFAULT 1): Estado activo/inactivo
- `created_at` (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP): Fecha de creación
- `updated_at` (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP ON UPDATE): Fecha de actualización

### Ejemplo de INSERT en Laravel:
```php
// Usando Eloquent
$role = new Role([
    'name' => 'Administrator',
    'description' => 'Full system access',
    'permissions' => json_encode([
        'users' => ['create', 'read', 'update', 'delete'],
        'products' => ['create', 'read', 'update', 'delete'],
        'batches' => ['create', 'read', 'update', 'delete']
    ]),
    'is_active' => 1
]);
$role->save();

// Usando Query Builder
DB::table('roles')->insert([
    'name' => 'Supervisor',
    'description' => 'Production supervisor',
    'permissions' => json_encode([
        'batches' => ['create', 'read', 'update'],
        'forms' => ['read', 'approve']
    ]),
    'is_active' => 1
]);
```

### Validaciones Laravel:
```php
$rules = [
    'name' => 'required|string|max:50|unique:roles,name',
    'description' => 'nullable|string',
    'permissions' => 'nullable|json',
    'is_active' => 'boolean'
];
```

---

## 2. Users

**Propósito:** Almacena la información básica de los usuarios del sistema.

### Estructura de Campos:
- `id` (BIGINT, AUTO_INCREMENT, PRIMARY KEY): Identificador único
- `name` (VARCHAR(100), NOT NULL): Nombre completo del usuario
- `email` (VARCHAR(100), NOT NULL, UNIQUE): Email único
- `password` (VARCHAR(255), NOT NULL): Contraseña hasheada
- `role_id` (BIGINT, NULL, FK a roles): Rol asignado
- `position` (VARCHAR(100), NULL): Cargo/posición
- `is_active` (TINYINT(1), DEFAULT 1): Estado activo/inactivo
- `last_login` (TIMESTAMP, NULL): Último login
- `created_at` (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP): Fecha de creación
- `updated_at` (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP ON UPDATE): Fecha de actualización

### Ejemplo de INSERT en Laravel:
```php
// Usando Eloquent
$user = new User([
    'name' => 'Juan Pérez',
    'email' => 'juan.perez@toliboy.com',
    'password' => Hash::make('password123'),
    'role_id' => 1, // ID del rol Administrator
    'position' => 'Production Manager',
    'is_active' => 1
]);
$user->save();

// Con relaciones
$user = User::create([
    'name' => 'María González',
    'email' => 'maria.gonzalez@toliboy.com',
    'password' => Hash::make('secure_password'),
    'position' => 'Quality Supervisor'
]);
$user->role()->associate(Role::find(2));
$user->save();
```

### Validaciones Laravel:
```php
$rules = [
    'name' => 'required|string|max:100',
    'email' => 'required|email|max:100|unique:users,email',
    'password' => 'required|string|min:8|confirmed',
    'role_id' => 'nullable|exists:roles,id',
    'position' => 'nullable|string|max:100',
    'is_active' => 'boolean'
];
```

---

## 3. Personal Data

**Propósito:** Extiende la información personal de los usuarios con datos adicionales.

### Estructura de Campos:
- `id` (BIGINT, AUTO_INCREMENT, PRIMARY KEY): Identificador único
- `user_id` (BIGINT, NOT NULL, UNIQUE, FK a users): Usuario asociado
- `num_phone` (VARCHAR(20), NULL): Teléfono principal
- `num_phone_alt` (VARCHAR(20), NULL): Teléfono alternativo
- `num_identification` (VARCHAR(50), NULL, UNIQUE): Número de identificación
- `identification_type` (VARCHAR(45), NULL): Tipo de identificación
- `address` (VARCHAR(255), NULL): Dirección
- `emergency_contact` (VARCHAR(100), NULL): Contacto de emergencia
- `emergency_phone` (VARCHAR(20), NULL): Teléfono de emergencia
- `created_at` (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP): Fecha de creación
- `updated_at` (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP ON UPDATE): Fecha de actualización

### Ejemplo de INSERT en Laravel:
```php
// Usando Eloquent
$personalData = new PersonalData([
    'user_id' => 1,
    'num_phone' => '+57 300 123 4567',
    'num_phone_alt' => '+57 301 987 6543',
    'num_identification' => '12345678',
    'identification_type' => 'CC', // Cédula de Ciudadanía
    'address' => 'Calle 123 #45-67, Bogotá',
    'emergency_contact' => 'Ana Pérez',
    'emergency_phone' => '+57 310 555 1234'
]);
$personalData->save();

// Con relación al usuario
$user = User::find(1);
$user->personalData()->create([
    'num_phone' => '+57 320 456 7890',
    'num_identification' => '87654321',
    'identification_type' => 'CE', // Cédula de Extranjería
    'address' => 'Carrera 7 #45-12, Medellín'
]);
```

### Validaciones Laravel:
```php
$rules = [
    'user_id' => 'required|exists:users,id|unique:personal_data,user_id',
    'num_phone' => 'nullable|string|max:20',
    'num_phone_alt' => 'nullable|string|max:20',
    'num_identification' => 'nullable|string|max:50|unique:personal_data,num_identification',
    'identification_type' => 'nullable|string|max:45',
    'address' => 'nullable|string|max:255',
    'emergency_contact' => 'nullable|string|max:100',
    'emergency_phone' => 'nullable|string|max:20'
];
```

---

## 4. Raw Materials

**Propósito:** Catálogo de materias primas utilizadas en la producción.

### Estructura de Campos:
- `id` (BIGINT, AUTO_INCREMENT, PRIMARY KEY): Identificador único
- `name` (VARCHAR(150), NOT NULL): Nombre de la materia prima
- `code` (VARCHAR(50), NOT NULL, UNIQUE): Código único
- `description` (TEXT, NULL): Descripción detallada
- `unit_of_measure` (VARCHAR(50), NOT NULL): Unidad de medida (kg, litros, unidades)
- `min_stock` (DECIMAL(10,2), DEFAULT 0): Stock mínimo permitido
- `is_active` (TINYINT(1), DEFAULT 1): Estado activo/inactivo
- `created_by` (BIGINT, NULL, FK a users): Usuario creador
- `created_at` (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP): Fecha de creación
- `updated_at` (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP ON UPDATE): Fecha de actualización

### Ejemplo de INSERT en Laravel:
```php
// Usando Eloquent
$rawMaterial = new RawMaterial([
    'name' => 'Harina de Trigo Tipo 1',
    'code' => 'HT001',
    'description' => 'Harina de trigo refinada para panadería',
    'unit_of_measure' => 'kg',
    'min_stock' => 100.00,
    'is_active' => 1,
    'created_by' => Auth::id()
]);
$rawMaterial->save();

// Múltiples inserción
$materials = [
    [
        'name' => 'Azúcar Refinada',
        'code' => 'AZ001',
        'description' => 'Azúcar refinada para repostería',
        'unit_of_measure' => 'kg',
        'min_stock' => 50.00,
        'created_by' => 1
    ],
    [
        'name' => 'Aceite Vegetal',
        'code' => 'AV001',
        'description' => 'Aceite vegetal comestible',
        'unit_of_measure' => 'litros',
        'min_stock' => 20.00,
        'created_by' => 1
    ]
];

foreach($materials as $material) {
    RawMaterial::create($material);
}
```

### Validaciones Laravel:
```php
$rules = [
    'name' => 'required|string|max:150',
    'code' => 'required|string|max:50|unique:raw_materials,code',
    'description' => 'nullable|string',
    'unit_of_measure' => 'required|string|max:50',
    'min_stock' => 'numeric|min:0',
    'is_active' => 'boolean',
    'created_by' => 'nullable|exists:users,id'
];
```

---

## 5. Products

**Propósito:** Catálogo de productos finales que se fabrican.

### Estructura de Campos:
- `id` (BIGINT, AUTO_INCREMENT, PRIMARY KEY): Identificador único
- `name` (VARCHAR(150), NOT NULL): Nombre del producto
- `code` (VARCHAR(50), NOT NULL, UNIQUE): Código único
- `category` (VARCHAR(100), NULL): Categoría del producto
- `description` (TEXT, NULL): Descripción detallada
- `specifications` (JSON, NULL): Especificaciones técnicas
- `unit_price` (DECIMAL(10,2), DEFAULT 0.00): Precio unitario
- `is_active` (TINYINT(1), DEFAULT 1): Estado activo/inactivo
- `created_by` (BIGINT, NULL, FK a users): Usuario creador
- `created_at` (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP): Fecha de creación
- `updated_at` (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP ON UPDATE): Fecha de actualización

### Ejemplo de INSERT en Laravel:
```php
// Usando Eloquent
$product = new Product([
    'name' => 'Pan Integral Familiar',
    'code' => 'PIF001',
    'category' => 'Panadería',
    'description' => 'Pan integral de 500g enriquecido con fibra',
    'specifications' => json_encode([
        'weight' => '500g',
        'ingredients' => ['harina integral', 'agua', 'levadura', 'sal'],
        'shelf_life' => '5 días',
        'storage' => 'ambiente seco'
    ]),
    'unit_price' => 2500.00,
    'is_active' => 1,
    'created_by' => Auth::id()
]);
$product->save();

// Con cast automático de JSON
class Product extends Model {
    protected $casts = [
        'specifications' => 'json'
    ];
}

// Entonces puedes hacer:
$product = Product::create([
    'name' => 'Galletas de Avena',
    'code' => 'GA001',
    'category' => 'Galletas',
    'specifications' => [
        'package_size' => '200g',
        'pieces_per_package' => 12,
        'nutritional_info' => [
            'calories_per_serving' => 150,
            'fat' => '6g',
            'carbs' => '22g'
        ]
    ],
    'unit_price' => 3200.00
]);
```

### Validaciones Laravel:
```php
$rules = [
    'name' => 'required|string|max:150',
    'code' => 'required|string|max:50|unique:products,code',
    'category' => 'nullable|string|max:100',
    'description' => 'nullable|string',
    'specifications' => 'nullable|json',
    'unit_price' => 'numeric|min:0',
    'is_active' => 'boolean',
    'created_by' => 'nullable|exists:users,id'
];
```

---

## 6. Batches

**Propósito:** Representa lotes de producción de productos específicos.

### Estructura de Campos:
- `id` (BIGINT, AUTO_INCREMENT, PRIMARY KEY): Identificador único
- `name` (VARCHAR(150), NOT NULL): Nombre del lote
- `code` (VARCHAR(50), NOT NULL, UNIQUE): Código único del lote
- `product_id` (BIGINT, NULL, FK a products): Producto a fabricar
- `start_date` (DATE, NOT NULL): Fecha de inicio
- `expected_end_date` (DATE, NULL): Fecha esperada de finalización
- `actual_end_date` (DATE, NULL): Fecha real de finalización
- `status` (ENUM, DEFAULT 'planned'): Estado del lote
  - Valores: 'planned', 'in_process', 'paused', 'completed', 'delivered', 'cancelled'
- `quantity` (INT, DEFAULT 0): Cantidad a producir
- `defect_quantity` (INT, DEFAULT 0): Cantidad defectuosa
- `notes` (TEXT, NULL): Notas adicionales
- `created_by` (BIGINT, NULL, FK a users): Usuario creador
- `created_at` (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP): Fecha de creación
- `updated_at` (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP ON UPDATE): Fecha de actualización

### Ejemplo de INSERT en Laravel:
```php
// Usando Eloquent
$batch = new Batch([
    'name' => 'Lote Pan Integral Enero 2024',
    'code' => 'L-PIF001-240101',
    'product_id' => 1, // ID del Pan Integral Familiar
    'start_date' => '2024-01-15',
    'expected_end_date' => '2024-01-17',
    'status' => 'planned',
    'quantity' => 1000,
    'defect_quantity' => 0,
    'notes' => 'Lote especial para promoción de enero',
    'created_by' => Auth::id()
]);
$batch->save();

// Usando Factory (para testing)
$batch = Batch::create([
    'name' => 'Lote Galletas Febrero',
    'code' => 'L-GA001-240201',
    'product_id' => Product::where('code', 'GA001')->first()->id,
    'start_date' => now()->format('Y-m-d'),
    'expected_end_date' => now()->addDays(3)->format('Y-m-d'),
    'quantity' => 500,
    'status' => 'planned'
]);

// Actualizar status del lote
$batch = Batch::find(1);
$batch->update([
    'status' => 'in_process',
    'actual_start_date' => now()
]);
```

### Estados del Lote:
```php
// En el modelo Batch
class Batch extends Model {
    const STATUS_PLANNED = 'planned';
    const STATUS_IN_PROCESS = 'in_process';
    const STATUS_PAUSED = 'paused';
    const STATUS_COMPLETED = 'completed';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_CANCELLED = 'cancelled';
    
    public static function getStatuses() {
        return [
            self::STATUS_PLANNED => 'Planificado',
            self::STATUS_IN_PROCESS => 'En Proceso',
            self::STATUS_PAUSED => 'Pausado',
            self::STATUS_COMPLETED => 'Completado',
            self::STATUS_DELIVERED => 'Entregado',
            self::STATUS_CANCELLED => 'Cancelado'
        ];
    }
}
```

### Validaciones Laravel:
```php
$rules = [
    'name' => 'required|string|max:150',
    'code' => 'required|string|max:50|unique:batches,code',
    'product_id' => 'nullable|exists:products,id',
    'start_date' => 'required|date',
    'expected_end_date' => 'nullable|date|after_or_equal:start_date',
    'actual_end_date' => 'nullable|date|after_or_equal:start_date',
    'status' => 'required|in:planned,in_process,paused,completed,delivered,cancelled',
    'quantity' => 'required|integer|min:1',
    'defect_quantity' => 'nullable|integer|min:0',
    'notes' => 'nullable|string',
    'created_by' => 'nullable|exists:users,id'
];
```

---

## 7. Inventory Movements

**Propósito:** Registra todos los movimientos de inventario de materias primas.

### Estructura de Campos:
- `id` (BIGINT, AUTO_INCREMENT, PRIMARY KEY): Identificador único
- `raw_material_id` (BIGINT, NOT NULL, FK a raw_materials): Materia prima
- `batch_id` (BIGINT, NULL, FK a batches): Lote asociado (si aplica)
- `movement_type` (ENUM, NOT NULL): Tipo de movimiento
  - Valores: 'in', 'out', 'adjustment'
- `quantity` (DECIMAL(10,2), NOT NULL): Cantidad del movimiento
- `unit_cost` (DECIMAL(10,2), DEFAULT 0.00): Costo unitario
- `notes` (TEXT, NULL): Notas del movimiento
- `created_by` (BIGINT, NULL, FK a users): Usuario que registra
- `created_at` (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP): Fecha de creación

### Ejemplo de INSERT en Laravel:
```php
// Entrada de inventario
$inventoryIn = new InventoryMovement([
    'raw_material_id' => 1, // Harina de Trigo
    'batch_id' => null, // No asociado a lote específico
    'movement_type' => 'in',
    'quantity' => 500.00, // 500 kg
    'unit_cost' => 2500.00, // $2500 por kg
    'notes' => 'Compra a proveedor ABC',
    'created_by' => Auth::id()
]);
$inventoryIn->save();

// Salida de inventario para producción
$inventoryOut = InventoryMovement::create([
    'raw_material_id' => 1, // Harina de Trigo
    'batch_id' => 1, // Para el lote L-PIF001-240101
    'movement_type' => 'out',
    'quantity' => -150.00, // 150 kg usados (negativo para salida)
    'unit_cost' => 2500.00,
    'notes' => 'Uso en producción de pan integral',
    'created_by' => Auth::id()
]);

// Ajuste de inventario
$adjustment = InventoryMovement::create([
    'raw_material_id' => 2, // Azúcar
    'movement_type' => 'adjustment',
    'quantity' => -5.00, // Ajuste por merma
    'notes' => 'Ajuste por inventario físico - merma detectada',
    'created_by' => Auth::id()
]);
```

### Cálculo de Stock Actual:
```php
// Método en el modelo RawMaterial
public function getCurrentStock() {
    return $this->inventoryMovements()
        ->sum('quantity');
}

// Método para obtener movimientos de un período
public function getMovementsByDateRange($startDate, $endDate) {
    return $this->inventoryMovements()
        ->whereBetween('created_at', [$startDate, $endDate])
        ->with(['batch', 'creator'])
        ->orderBy('created_at', 'desc')
        ->get();
}
```

### Validaciones Laravel:
```php
$rules = [
    'raw_material_id' => 'required|exists:raw_materials,id',
    'batch_id' => 'nullable|exists:batches,id',
    'movement_type' => 'required|in:in,out,adjustment',
    'quantity' => 'required|numeric|not_in:0',
    'unit_cost' => 'nullable|numeric|min:0',
    'notes' => 'nullable|string',
    'created_by' => 'nullable|exists:users,id'
];

// Validación custom para salidas
$rules['quantity'] = [
    'required',
    'numeric',
    'not_in:0',
    function ($attribute, $value, $fail) use ($request) {
        if ($request->movement_type === 'out' && $value > 0) {
            $fail('Las salidas deben tener cantidad negativa.');
        }
        if ($request->movement_type === 'in' && $value < 0) {
            $fail('Las entradas deben tener cantidad positiva.');
        }
    }
];
```

---

## 8. Forms

**Propósito:** Define formularios dinámicos para captura de datos en el sistema.

### Estructura de Campos:
- `id` (BIGINT, AUTO_INCREMENT, PRIMARY KEY): Identificador único
- `name` (VARCHAR(150), NOT NULL): Nombre del formulario
- `code` (VARCHAR(50), NOT NULL): Código del formulario
- `description` (TEXT, NULL): Descripción del formulario
- `version` (VARCHAR(20), DEFAULT '1.0'): Versión del formulario
- `created_by` (BIGINT, NULL, FK a users): Usuario creador
- `is_active` (TINYINT(1), DEFAULT 1): Estado activo/inactivo
- `display_order` (INT, DEFAULT 1): Orden de visualización
- `created_at` (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP): Fecha de creación
- `updated_at` (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP ON UPDATE): Fecha de actualización

**Índice Único:** `code` + `version` deben ser únicos juntos.

### Ejemplo de INSERT en Laravel:
```php
// Formulario de Control de Calidad
$form = new Form([
    'name' => 'Control de Calidad - Pan',
    'code' => 'QC_BREAD',
    'description' => 'Formulario para control de calidad en producción de pan',
    'version' => '1.0',
    'created_by' => Auth::id(),
    'is_active' => 1,
    'display_order' => 1
]);
$form->save();

// Nueva versión del formulario
$newVersion = Form::create([
    'name' => 'Control de Calidad - Pan (Actualizado)',
    'code' => 'QC_BREAD',
    'description' => 'Formulario actualizado con nuevos campos de calidad',
    'version' => '1.1',
    'created_by' => Auth::id(),
    'is_active' => 1,
    'display_order' => 1
]);

// Desactivar versión anterior
Form::where('code', 'QC_BREAD')
    ->where('version', '1.0')
    ->update(['is_active' => 0]);
```

### Validaciones Laravel:
```php
$rules = [
    'name' => 'required|string|max:150',
    'code' => [
        'required',
        'string',
        'max:50',
        Rule::unique('forms')->where(function ($query) use ($request) {
            return $query->where('version', $request->version);
        })
    ],
    'description' => 'nullable|string',
    'version' => 'required|string|max:20',
    'created_by' => 'nullable|exists:users,id',
    'is_active' => 'boolean',
    'display_order' => 'integer|min:1'
];
```

---

## 9. Form Fields

**Propósito:** Define los campos individuales que componen cada formulario.

### Estructura de Campos:
- `id` (BIGINT, AUTO_INCREMENT, PRIMARY KEY): Identificador único
- `form_id` (BIGINT, NOT NULL, FK a forms): Formulario al que pertenece
- `label` (VARCHAR(255), NOT NULL): Etiqueta del campo
- `field_code` (VARCHAR(100), NOT NULL): Código único del campo dentro del form
- `type` (ENUM, DEFAULT 'text'): Tipo de campo
  - Valores: 'text', 'number', 'date', 'time', 'select', 'checkbox', 'radio', 'textarea', 'file'
- `required` (TINYINT(1), DEFAULT 0): Si es obligatorio
- `options` (JSON, NULL): Opciones para campos select/radio/checkbox
- `validation_rules` (JSON, NULL): Reglas de validación
- `field_order` (INT, DEFAULT 0): Orden del campo en el formulario
- `is_active` (TINYINT(1), DEFAULT 1): Estado activo/inactivo
- `created_at` (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP): Fecha de creación
- `updated_at` (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP ON UPDATE): Fecha de actualización

**Índice Único:** `form_id` + `field_code` deben ser únicos juntos.

### Ejemplo de INSERT en Laravel:
```php
// Campos para el formulario de Control de Calidad
$formId = 1; // ID del formulario QC_BREAD

$fields = [
    [
        'form_id' => $formId,
        'label' => 'Temperatura del Horno',
        'field_code' => 'oven_temperature',
        'type' => 'number',
        'required' => 1,
        'validation_rules' => json_encode([
            'min' => 180,
            'max' => 220,
            'step' => 1
        ]),
        'field_order' => 1
    ],
    [
        'form_id' => $formId,
        'label' => 'Tiempo de Cocción (minutos)',
        'field_code' => 'cooking_time',
        'type' => 'number',
        'required' => 1,
        'validation_rules' => json_encode([
            'min' => 15,
            'max' => 45
        ]),
        'field_order' => 2
    ],
    [
        'form_id' => $formId,
        'label' => 'Color del Pan',
        'field_code' => 'bread_color',
        'type' => 'select',
        'required' => 1,
        'options' => json_encode([
            'light' => 'Claro',
            'medium' => 'Medio',
            'dark' => 'Oscuro'
        ]),
        'field_order' => 3
    ],
    [
        'form_id' => $formId,
        'label' => '¿Pasa Control de Calidad?',
        'field_code' => 'quality_approved',
        'type' => 'radio',
        'required' => 1,
        'options' => json_encode([
            'yes' => 'Sí',
            'no' => 'No'
        ]),
        'field_order' => 4
    ],
    [
        'form_id' => $formId,
        'label' => 'Observaciones',
        'field_code' => 'observations',
        'type' => 'textarea',
        'required' => 0,
        'validation_rules' => json_encode([
            'max_length' => 500
        ]),
        'field_order' => 5
    ]
];

foreach($fields as $field) {
    FormField::create($field);
}

// Campo con validaciones complejas
$advancedField = FormField::create([
    'form_id' => $formId,
    'label' => 'Peso del Pan (gramos)',
    'field_code' => 'bread_weight',
    'type' => 'number',
    'required' => 1,
    'validation_rules' => json_encode([
        'min' => 450,
        'max' => 550,
        'step' => 0.1,
        'error_messages' => [
            'min' => 'El peso mínimo debe ser 450g',
            'max' => 'El peso máximo debe ser 550g'
        ]
    ]),
    'field_order' => 6
]);
```

### Tipos de Campos y sus Usos:
```php
// En el modelo FormField
class FormField extends Model {
    const TYPE_TEXT = 'text';
    const TYPE_NUMBER = 'number';
    const TYPE_DATE = 'date';
    const TYPE_TIME = 'time';
    const TYPE_SELECT = 'select';
    const TYPE_CHECKBOX = 'checkbox';
    const TYPE_RADIO = 'radio';
    const TYPE_TEXTAREA = 'textarea';
    const TYPE_FILE = 'file';
    
    public static function getFieldTypes() {
        return [
            self::TYPE_TEXT => 'Texto',
            self::TYPE_NUMBER => 'Número',
            self::TYPE_DATE => 'Fecha',
            self::TYPE_TIME => 'Hora',
            self::TYPE_SELECT => 'Lista Desplegable',
            self::TYPE_CHECKBOX => 'Casilla de Verificación',
            self::TYPE_RADIO => 'Opción Múltiple',
            self::TYPE_TEXTAREA => 'Área de Texto',
            self::TYPE_FILE => 'Archivo'
        ];
    }
}
```

### Validaciones Laravel:
```php
$rules = [
    'form_id' => 'required|exists:forms,id',
    'label' => 'required|string|max:255',
    'field_code' => [
        'required',
        'string',
        'max:100',
        Rule::unique('form_fields')->where(function ($query) use ($request) {
            return $query->where('form_id', $request->form_id);
        })
    ],
    'type' => 'required|in:text,number,date,time,select,checkbox,radio,textarea,file',
    'required' => 'boolean',
    'options' => 'nullable|json',
    'validation_rules' => 'nullable|json',
    'field_order' => 'integer|min:0',
    'is_active' => 'boolean'
];
```

---

## 10. Form Responses

**Propósito:** Almacena las respuestas/instancias de formularios completados por los usuarios.

### Estructura de Campos:
- `id` (BIGINT, AUTO_INCREMENT, PRIMARY KEY): Identificador único
- `form_id` (BIGINT, NOT NULL, FK a forms): Formulario respondido
- `user_id` (BIGINT, NOT NULL, FK a users): Usuario que responde
- `batch_id` (BIGINT, NULL, FK a batches): Lote asociado (si aplica)
- `status` (ENUM, DEFAULT 'pending'): Estado de la respuesta
  - Valores: 'pending', 'in_progress', 'completed', 'approved', 'rejected'
- `submitted_at` (TIMESTAMP, NULL): Fecha de envío
- `reviewed_by` (BIGINT, NULL, FK a users): Usuario revisor
- `reviewed_at` (TIMESTAMP, NULL): Fecha de revisión
- `review_notes` (TEXT, NULL): Notas de la revisión
- `created_at` (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP): Fecha de creación
- `updated_at` (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP ON UPDATE): Fecha de actualización

### Ejemplo de INSERT en Laravel:
```php
// Crear nueva respuesta de formulario
$response = new FormResponse([
    'form_id' => 1, // Control de Calidad - Pan
    'user_id' => Auth::id(),
    'batch_id' => 1, // Lote L-PIF001-240101
    'status' => 'in_progress'
]);
$response->save();

// Respuesta completa con envío
$response = FormResponse::create([
    'form_id' => 1,
    'user_id' => 2, // ID del operador
    'batch_id' => 1,
    'status' => 'completed',
    'submitted_at' => now()
]);

// Aprobar/rechazar respuesta
$response = FormResponse::find(1);
$response->update([
    'status' => 'approved',
    'reviewed_by' => Auth::id(),
    'reviewed_at' => now(),
    'review_notes' => 'Control de calidad cumple con los estándares'
]);

// Respuesta con método de conveniencia
class FormResponse extends Model {
    public function submit() {
        $this->update([
            'status' => 'completed',
            'submitted_at' => now()
        ]);
    }
    
    public function approve($reviewerId, $notes = null) {
        $this->update([
            'status' => 'approved',
            'reviewed_by' => $reviewerId,
            'reviewed_at' => now(),
            'review_notes' => $notes
        ]);
    }
    
    public function reject($reviewerId, $notes) {
        $this->update([
            'status' => 'rejected',
            'reviewed_by' => $reviewerId,
            'reviewed_at' => now(),
            'review_notes' => $notes
        ]);
    }
}
```

### Estados de Respuesta:
```php
class FormResponse extends Model {
    const STATUS_PENDING = 'pending';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    
    public static function getStatuses() {
        return [
            self::STATUS_PENDING => 'Pendiente',
            self::STATUS_IN_PROGRESS => 'En Progreso',
            self::STATUS_COMPLETED => 'Completado',
            self::STATUS_APPROVED => 'Aprobado',
            self::STATUS_REJECTED => 'Rechazado'
        ];
    }
}
```

### Validaciones Laravel:
```php
$rules = [
    'form_id' => 'required|exists:forms,id',
    'user_id' => 'required|exists:users,id',
    'batch_id' => 'nullable|exists:batches,id',
    'status' => 'required|in:pending,in_progress,completed,approved,rejected',
    'submitted_at' => 'nullable|date',
    'reviewed_by' => 'nullable|exists:users,id',
    'reviewed_at' => 'nullable|date',
    'review_notes' => 'nullable|string'
];
```

---

## 11. Form Response Values

**Propósito:** Almacena los valores específicos de cada campo en las respuestas de formularios.

### Estructura de Campos:
- `id` (BIGINT, AUTO_INCREMENT, PRIMARY KEY): Identificador único
- `response_id` (BIGINT, NOT NULL, FK a form_responses): Respuesta asociada
- `field_id` (BIGINT, NOT NULL, FK a form_fields): Campo del formulario
- `value` (TEXT, NULL): Valor del campo
- `file_path` (VARCHAR(500), NULL): Ruta del archivo (para campos file)
- `created_at` (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP): Fecha de creación
- `updated_at` (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP ON UPDATE): Fecha de actualización

**Índice Único:** `response_id` + `field_id` deben ser únicos juntos.

### Ejemplo de INSERT en Laravel:
```php
// Guardar valores de formulario
$responseId = 1;
$formData = [
    'oven_temperature' => '200',
    'cooking_time' => '25',
    'bread_color' => 'medium',
    'quality_approved' => 'yes',
    'observations' => 'Pan bien dorado, textura perfecta',
    'bread_weight' => '495.5'
];

foreach($formData as $fieldCode => $value) {
    // Obtener el field_id basado en el código
    $field = FormField::where('form_id', 1)
        ->where('field_code', $fieldCode)
        ->first();
    
    if($field) {
        FormResponseValue::create([
            'response_id' => $responseId,
            'field_id' => $field->id,
            'value' => $value
        ]);
    }
}

// Método más eficiente usando transacciones
DB::transaction(function() use ($responseId, $formData) {
    $fields = FormField::where('form_id', 1)
        ->whereIn('field_code', array_keys($formData))
        ->get()
        ->keyBy('field_code');
    
    $values = [];
    foreach($formData as $fieldCode => $value) {
        if(isset($fields[$fieldCode])) {
            $values[] = [
                'response_id' => $responseId,
                'field_id' => $fields[$fieldCode]->id,
                'value' => $value,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }
    }
    
    FormResponseValue::insert($values);
});

// Para campos de archivo
$fileField = FormField::where('field_code', 'quality_photo')->first();
if($request->hasFile('quality_photo')) {
    $filePath = $request->file('quality_photo')->store('form_uploads', 'public');
    
    FormResponseValue::create([
        'response_id' => $responseId,
        'field_id' => $fileField->id,
        'file_path' => $filePath
    ]);
}
```

### Métodos de Conveniencia:
```php
// En el modelo FormResponse
class FormResponse extends Model {
    public function getFieldValue($fieldCode) {
        return $this->values()
            ->whereHas('field', function($query) use ($fieldCode) {
                $query->where('field_code', $fieldCode);
            })
            ->first()
            ->value ?? null;
    }
    
    public function setFieldValue($fieldCode, $value, $filePath = null) {
        $field = $this->form->fields()
            ->where('field_code', $fieldCode)
            ->first();
            
        if(!$field) return false;
        
        return $this->values()->updateOrCreate(
            ['field_id' => $field->id],
            [
                'value' => $value,
                'file_path' => $filePath
            ]
        );
    }
    
    public function getFormattedValues() {
        $values = [];
        foreach($this->values as $value) {
            $values[$value->field->field_code] = [
                'label' => $value->field->label,
                'value' => $value->value,
                'file_path' => $value->file_path,
                'type' => $value->field->type
            ];
        }
        return $values;
    }
}
```

### Validaciones Laravel:
```php
$rules = [
    'response_id' => 'required|exists:form_responses,id',
    'field_id' => [
        'required',
        'exists:form_fields,id',
        Rule::unique('form_response_values')->where(function ($query) use ($request) {
            return $query->where('response_id', $request->response_id);
        })
    ],
    'value' => 'nullable|string',
    'file_path' => 'nullable|string|max:500'
];

// Validación dinámica basada en el tipo de campo
public function validateFieldValue($field, $value) {
    $rules = [];
    
    switch($field->type) {
        case 'number':
            $rules[] = 'numeric';
            break;
        case 'date':
            $rules[] = 'date';
            break;
        case 'time':
            $rules[] = 'date_format:H:i';
            break;
        case 'email':
            $rules[] = 'email';
            break;
    }
    
    if($field->required) {
        $rules[] = 'required';
    }
    
    // Aplicar reglas de validación personalizadas
    if($field->validation_rules) {
        $customRules = json_decode($field->validation_rules, true);
        if(isset($customRules['min'])) $rules[] = "min:{$customRules['min']}";
        if(isset($customRules['max'])) $rules[] = "max:{$customRules['max']}";
    }
    
    return $rules;
}
```

---

## 12. Work Logs

**Propósito:** Registra las horas de trabajo de los empleados, asociadas opcionalmente a lotes específicos.

### Estructura de Campos:
- `id` (BIGINT, AUTO_INCREMENT, PRIMARY KEY): Identificador único
- `user_id` (BIGINT, NOT NULL, FK a users): Usuario/empleado
- `date` (DATE, NOT NULL): Fecha del trabajo
- `start_time` (TIME, NULL): Hora de inicio
- `end_time` (TIME, NULL): Hora de finalización
- `total_hours` (DECIMAL(5,2), VIRTUAL): Horas totales (calculado automáticamente)
- `overtime_hours` (DECIMAL(5,2), DEFAULT 0.00): Horas extra
- `batch_id` (BIGINT, NULL, FK a batches): Lote asociado
- `task_description` (TEXT, NULL): Descripción de la tarea
- `notes` (TEXT, NULL): Notas adicionales
- `created_at` (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP): Fecha de creación
- `updated_at` (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP ON UPDATE): Fecha de actualización

**Nota:** El campo `total_hours` es calculado automáticamente por MySQL usando una columna virtual.

### Ejemplo de INSERT en Laravel:
```php
// Registro de trabajo simple
$workLog = new WorkLog([
    'user_id' => 2,
    'date' => '2024-01-15',
    'start_time' => '08:00:00',
    'end_time' => '17:00:00',
    'overtime_hours' => 1.00, // 1 hora extra
    'batch_id' => 1,
    'task_description' => 'Producción de pan integral - Lote L-PIF001-240101',
    'notes' => 'Todo transcurrió sin novedad'
]);
$workLog->save();

// Múltiples registros para un día
$workLogs = [
    [
        'user_id' => 3,
        'date' => '2024-01-15',
        'start_time' => '06:00:00',
        'end_time' => '14:00:00',
        'batch_id' => 1,
        'task_description' => 'Preparación de masa y horneado',
    ],
    [
        'user_id' => 4,
        'date' => '2024-01-15',
        'start_time' => '14:00:00',
        'end_time' => '22:00:00',
        'batch_id' => 2,
        'task_description' => 'Empaque y almacenamiento',
    ]
];

foreach($workLogs as $log) {
    WorkLog::create($log);
}

// Check-in/Check-out en tiempo real
class WorkLog extends Model {
    public static function checkIn($userId, $batchId = null, $taskDescription = null) {
        return self::create([
            'user_id' => $userId,
            'date' => now()->format('Y-m-d'),
            'start_time' => now()->format('H:i:s'),
            'batch_id' => $batchId,
            'task_description' => $taskDescription
        ]);
    }
    
    public static function checkOut($userId, $notes = null) {
        $workLog = self::where('user_id', $userId)
            ->where('date', now()->format('Y-m-d'))
            ->whereNull('end_time')
            ->latest()
            ->first();
            
        if($workLog) {
            $workLog->update([
                'end_time' => now()->format('H:i:s'),
                'notes' => $notes
            ]);
            
            // Calcular horas extra si es necesario
            $totalHours = $workLog->calculateTotalHours();
            if($totalHours > 8) {
                $workLog->update([
                    'overtime_hours' => $totalHours - 8
                ]);
            }
        }
        
        return $workLog;
    }
    
    public function calculateTotalHours() {
        if(!$this->start_time || !$this->end_time) return 0;
        
        $start = Carbon::parse($this->date . ' ' . $this->start_time);
        $end = Carbon::parse($this->date . ' ' . $this->end_time);
        
        return $end->diffInHours($start, true);
    }
}
```

### Reportes de Trabajo:
```php
// Reporte semanal por usuario
public function getWeeklyHours($userId, $startDate) {
    return WorkLog::where('user_id', $userId)
        ->whereBetween('date', [
            $startDate,
            Carbon::parse($startDate)->addDays(6)
        ])
        ->selectRaw('
            SUM(TIMESTAMPDIFF(HOUR, CONCAT(date, " ", start_time), CONCAT(date, " ", end_time))) as total_hours,
            SUM(overtime_hours) as total_overtime
        ')
        ->first();
}

// Reporte por lote
public function getBatchWorkHours($batchId) {
    return WorkLog::where('batch_id', $batchId)
        ->with('user:id,name')
        ->selectRaw('
            user_id,
            SUM(TIMESTAMPDIFF(HOUR, CONCAT(date, " ", start_time), CONCAT(date, " ", end_time))) as total_hours,
            COUNT(*) as days_worked
        ')
        ->groupBy('user_id')
        ->get();
}
```

### Validaciones Laravel:
```php
$rules = [
    'user_id' => 'required|exists:users,id',
    'date' => 'required|date',
    'start_time' => 'nullable|date_format:H:i:s',
    'end_time' => 'nullable|date_format:H:i:s|after:start_time',
    'overtime_hours' => 'nullable|numeric|min:0|max:12',
    'batch_id' => 'nullable|exists:batches,id',
    'task_description' => 'nullable|string',
    'notes' => 'nullable|string'
];

// Validación personalizada para evitar solapamientos
$rules['start_time'] = [
    'nullable',
    'date_format:H:i:s',
    function ($attribute, $value, $fail) use ($request) {
        $existing = WorkLog::where('user_id', $request->user_id)
            ->where('date', $request->date)
            ->where('id', '!=', $request->route('worklog'))
            ->where(function($query) use ($request) {
                $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                      ->orWhereBetween('end_time', [$request->start_time, $request->end_time]);
            })
            ->exists();
            
        if($existing) {
            $fail('Las horas se solapan con otro registro existente.');
        }
    }
];
```

---

## 13. Notifications

**Propósito:** Sistema de notificaciones para usuarios del sistema.

### Estructura de Campos:
- `id` (BIGINT, AUTO_INCREMENT, PRIMARY KEY): Identificador único
- `user_id` (BIGINT, NOT NULL, FK a users): Usuario destinatario
- `title` (VARCHAR(255), NOT NULL): Título de la notificación
- `message` (TEXT, NOT NULL): Mensaje de la notificación
- `type` (ENUM, DEFAULT 'info'): Tipo de notificación
  - Valores: 'info', 'warning', 'error', 'success'
- `is_read` (TINYINT(1), DEFAULT 0): Estado de lectura
- `related_table` (VARCHAR(100), NULL): Tabla relacionada
- `related_id` (BIGINT, NULL): ID del registro relacionado
- `expires_at` (TIMESTAMP, NULL): Fecha de expiración
- `created_at` (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP): Fecha de creación

### Ejemplo de INSERT en Laravel:
```php
// Notificación simple
$notification = new Notification([
    'user_id' => 1,
    'title' => 'Lote Completado',
    'message' => 'El lote L-PIF001-240101 ha sido completado exitosamente.',
    'type' => 'success',
    'related_table' => 'batches',
    'related_id' => 1
]);
$notification->save();

// Notificación de stock bajo
Notification::create([
    'user_id' => 2, // Supervisor de inventario
    'title' => 'Stock Bajo',
    'message' => 'La materia prima "Harina de Trigo" está por debajo del mínimo (50kg restantes).',
    'type' => 'warning',
    'related_table' => 'raw_materials',
    'related_id' => 1
]);

// Notificación con expiración
Notification::create([
    'user_id' => 3,
    'title' => 'Recordatorio: Formulario Pendiente',
    'message' => 'Tienes un formulario de control de calidad pendiente por completar.',
    'type' => 'info',
    'related_table' => 'form_responses',
    'related_id' => 5,
    'expires_at' => now()->addDays(7)
]);

// Notificar a múltiples usuarios
$userIds = [1, 2, 3];
$notifications = [];

foreach($userIds as $userId) {
    $notifications[] = [
        'user_id' => $userId,
        'title' => 'Mantenimiento Programado',
        'message' => 'El sistema estará en mantenimiento mañana de 2:00 AM a 4:00 AM.',
        'type' => 'info',
        'created_at' => now(),
    ];
}

Notification::insert($notifications);
```

### Métodos de Conveniencia:
```php
class Notification extends Model {
    const TYPE_INFO = 'info';
    const TYPE_WARNING = 'warning';
    const TYPE_ERROR = 'error';
    const TYPE_SUCCESS = 'success';
    
    // Marcar como leída
    public function markAsRead() {
        $this->update(['is_read' => 1]);
    }
    
    // Notificaciones no leídas de un usuario
    public static function unreadForUser($userId) {
        return self::where('user_id', $userId)
            ->where('is_read', 0)
            ->where(function($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }
    
    // Contar notificaciones no leídas
    public static function unreadCountForUser($userId) {
        return self::where('user_id', $userId)
            ->where('is_read', 0)
            ->where(function($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
            })
            ->count();
    }
    
    // Limpiar notificaciones expiradas
    public static function cleanExpired() {
        return self::where('expires_at', '<', now())->delete();
    }
}

// Facade para notificaciones
class NotificationService {
    public static function notifyLowStock($rawMaterialId, $currentStock, $minStock) {
        $material = RawMaterial::find($rawMaterialId);
        $supervisors = User::whereHas('role', function($query) {
            $query->where('name', 'Supervisor');
        })->get();
        
        foreach($supervisors as $supervisor) {
            Notification::create([
                'user_id' => $supervisor->id,
                'title' => 'Stock Bajo: ' . $material->name,
                'message' => "Stock actual: {$currentStock} {$material->unit_of_measure}. Mínimo requerido: {$minStock} {$material->unit_of_measure}.",
                'type' => 'warning',
                'related_table' => 'raw_materials',
                'related_id' => $rawMaterialId
            ]);
        }
    }
    
    public static function notifyBatchStatusChange($batchId, $oldStatus, $newStatus) {
        $batch = Batch::with('product', 'creator')->find($batchId);
        
        Notification::create([
            'user_id' => $batch->created_by,
            'title' => "Cambio de Estado: {$batch->name}",
            'message' => "El lote ha cambiado de '{$oldStatus}' a '{$newStatus}'.",
            'type' => 'info',
            'related_table' => 'batches',
            'related_id' => $batchId
        ]);
    }
    
    public static function notifyFormApproval($responseId, $approved, $reviewNotes = null) {
        $response = FormResponse::with('user', 'form')->find($responseId);
        
        Notification::create([
            'user_id' => $response->user_id,
            'title' => $approved ? 'Formulario Aprobado' : 'Formulario Rechazado',
            'message' => ($approved ? 'Tu formulario ' : 'Tu formulario fue rechazado: ') 
                        . $response->form->name 
                        . ($reviewNotes ? ". Notas: {$reviewNotes}" : ''),
            'type' => $approved ? 'success' : 'error',
            'related_table' => 'form_responses',
            'related_id' => $responseId
        ]);
    }
}
```

### Validaciones Laravel:
```php
$rules = [
    'user_id' => 'required|exists:users,id',
    'title' => 'required|string|max:255',
    'message' => 'required|string',
    'type' => 'required|in:info,warning,error,success',
    'is_read' => 'boolean',
    'related_table' => 'nullable|string|max:100',
    'related_id' => 'nullable|integer',
    'expires_at' => 'nullable|date|after:now'
];
```

---

## Relaciones y Dependencias

### Diagrama de Relaciones:

```
users (1) ←→ (1) personal_data
users (1) ←→ (*) raw_materials (created_by)
users (1) ←→ (*) products (created_by)
users (1) ←→ (*) batches (created_by)
users (1) ←→ (*) inventory_movements (created_by)
users (1) ←→ (*) forms (created_by)
users (1) ←→ (*) form_responses (user_id, reviewed_by)
users (1) ←→ (*) work_logs
users (1) ←→ (*) notifications

roles (1) ←→ (*) users

products (1) ←→ (*) batches

raw_materials (1) ←→ (*) inventory_movements

batches (1) ←→ (*) inventory_movements
batches (1) ←→ (*) form_responses
batches (1) ←→ (*) work_logs

forms (1) ←→ (*) form_fields
forms (1) ←→ (*) form_responses

form_responses (1) ←→ (*) form_response_values
form_fields (1) ←→ (*) form_response_values
```

### Modelos Eloquent y Relaciones:

```php
// User.php
class User extends Model {
    public function role() {
        return $this->belongsTo(Role::class);
    }
    
    public function personalData() {
        return $this->hasOne(PersonalData::class);
    }
    
    public function createdRawMaterials() {
        return $this->hasMany(RawMaterial::class, 'created_by');
    }
    
    public function createdProducts() {
        return $this->hasMany(Product::class, 'created_by');
    }
    
    public function createdBatches() {
        return $this->hasMany(Batch::class, 'created_by');
    }
    
    public function formResponses() {
        return $this->hasMany(FormResponse::class);
    }
    
    public function reviewedResponses() {
        return $this->hasMany(FormResponse::class, 'reviewed_by');
    }
    
    public function workLogs() {
        return $this->hasMany(WorkLog::class);
    }
    
    public function notifications() {
        return $this->hasMany(Notification::class);
    }
}

// Batch.php
class Batch extends Model {
    public function product() {
        return $this->belongsTo(Product::class);
    }
    
    public function creator() {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    public function inventoryMovements() {
        return $this->hasMany(InventoryMovement::class);
    }
    
    public function formResponses() {
        return $this->hasMany(FormResponse::class);
    }
    
    public function workLogs() {
        return $this->hasMany(WorkLog::class);
    }
}

// FormResponse.php
class FormResponse extends Model {
    public function form() {
        return $this->belongsTo(Form::class);
    }
    
    public function user() {
        return $this->belongsTo(User::class);
    }
    
    public function batch() {
        return $this->belongsTo(Batch::class);
    }
    
    public function reviewer() {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
    
    public function values() {
        return $this->hasMany(FormResponseValue::class, 'response_id');
    }
}
```

## Orden de Inserción de Datos


El siguiente orden asegura que las **Foreign Keys** se respeten al insertar datos.

---

## 1. Usuarios
Tabla: `users`  
👉 Entidad base, todos los demás registros dependen de esta.

```sql
INSERT INTO users (id, name, email, password) VALUES (...);
```

---

## 2. Datos Personales
Tabla: `personal_data`  
🔗 FK: `user_id` → `users.id`

```sql
INSERT INTO personal_data (user_id, num_phone, address, ...) VALUES (...);
```

---

## 3. Materias Primas
Tabla: `raw_materials`  
🔗 FK: `created_by` → `users.id`

```sql
INSERT INTO raw_materials (name, code, unit_of_measure, created_by) VALUES (...);
```

---

## 4. Productos
Tabla: `products`  
🔗 FK: `created_by` → `users.id`

```sql
INSERT INTO products (name, code, category, unit_price, created_by) VALUES (...);
```

---

## 5. Lotes de Producción
Tabla: `batches`  
🔗 FK: `product_id` → `products.id`  
🔗 FK: `created_by` → `users.id`

```sql
INSERT INTO batches (name, code, product_id, start_date, created_by) VALUES (...);
```

---

## 6. Movimientos de Inventario
Tabla: `inventory_movements`  
🔗 FK: `raw_material_id` → `raw_materials.id`  
🔗 FK: `batch_id` → `batches.id`  
🔗 FK: `created_by` → `users.id`

```sql
INSERT INTO inventory_movements (raw_material_id, batch_id, movement_type, quantity, created_by) VALUES (...);
```

---

## 7. Formularios
Tabla: `forms`  
🔗 FK: `created_by` → `users.id`

```sql
INSERT INTO forms (name, code, version, created_by) VALUES (...);
```

---

## 8. Campos de Formularios
Tabla: `form_fields`  
🔗 FK: `form_id` → `forms.id`

```sql
INSERT INTO form_fields (form_id, label, field_code, type, required) VALUES (...);
```

---

## 9. Respuestas de Formularios
Tabla: `form_responses`  
🔗 FK: `form_id` → `forms.id`  
🔗 FK: `user_id` → `users.id`  
🔗 FK: `batch_id` → `batches.id`

```sql
INSERT INTO form_responses (form_id, user_id, batch_id, status) VALUES (...);
```

---

## 10. Valores de Respuestas
Tabla: `form_response_values`  
🔗 FK: `response_id` → `form_responses.id`  
🔗 FK: `field_id` → `form_fields.id`

```sql
INSERT INTO form_response_values (response_id, field_id, value, file_path) VALUES (...);
```

---

## 11. Registros de Trabajo
Tabla: `work_logs`  
🔗 FK: `user_id` → `users.id`  
🔗 FK: `batch_id` → `batches.id`

```sql
INSERT INTO work_logs (user_id, date, start_time, end_time, batch_id, task_description) VALUES (...);
```

---

## 12. Notificaciones
Tabla: `notifications`  
🔗 FK: `user_id` → `users.id`  
🔗 `related_table`/`related_id` → opcional (ej: `batches`, `forms`, etc.)

```sql
INSERT INTO notifications (user_id, title, message, type, related_table, related_id) VALUES (...);
```
