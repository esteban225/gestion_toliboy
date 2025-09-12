# API Documentation - Gestión Toliboy

## Autenticación

Todas las APIs protegidas requieren un token JWT en el header:
```
Authorization: Bearer <token>
```

## Endpoints de Autenticación

### POST /api/register
Registro de nuevos usuarios.

**Request Body:**
```json
{
    "name": "string (required, max:255)",
    "email": "string (required, email, unique)",
    "password": "string (required, min:8)"
}
```

**Response (201):**
```json
{
    "success": true,
    "token": "jwt_token_here",
    "user": {
        "id": 1,
        "name": "Usuario Test",
        "email": "test@example.com",
        "role_id": 1
    }
}
```

**Errores (422):**
```json
{
    "success": false,
    "errors": {
        "email": ["El correo electrónico ya está en uso"],
        "password": ["La contraseña debe tener al menos 8 caracteres"]
    }
}
```

### POST /api/login
Iniciar sesión de usuarios existentes.

**Request Body:**
```json
{
    "email": "string (required, email)",
    "password": "string (required, min:8)"
}
```

**Response (200):**
```json
{
    "success": true,
    "user": {
        "id": 1,
        "name": "Usuario Test",
        "email": "test@example.com",
        "role_id": 1
    },
    "token": "jwt_token_here",
    "status": 200
}
```

**Errores (401):**
```json
{
    "success": false,
    "message": "Credenciales inválidas"
}
```

### POST /api/logout
Cerrar sesión (invalidar token). Requiere autenticación.

**Response (200):**
```json
{
    "message": "Sesión cerrada correctamente"
}
```

### POST /api/refresh
Refrescar token JWT. Requiere autenticación.

**Response (200):**
```json
{
    "access_token": "new_jwt_token_here",
    "token_type": "bearer",
    "expires_in": 3600
}
```

### GET /api/me
Obtener información del usuario autenticado. Requiere autenticación y rol Developer.

**Response (200):**
```json
{
    "id": 1,
    "name": "Usuario Test",
    "email": "test@example.com",
    "role_id": 1,
    "position": "Developer",
    "is_active": true,
    "last_login": "2024-01-01T10:00:00Z",
    "role": {
        "id": 1,
        "name": "Developer",
        "description": "Full access role"
    }
}
```

## Endpoints de Datos Personales

### GET /api/userData
Listar todos los datos personales. Requiere rol Developer.

**Response (201):**
```json
{
    "status": true,
    "message": "Usuarios encontrados",
    "data": [
        {
            "id": 1,
            "user_id": 1,
            "num_phone": "1234567890",
            "num_phone_alt": "0987654321",
            "num_identification": "12345678",
            "identification_type": "CC",
            "address": "Calle 123 #45-67",
            "emergency_contact": "María García",
            "emergency_phone": "1111111111"
        }
    ]
}
```

**Sin datos (404):**
```json
{
    "message": "No se encuentran datos de usuarios"
}
```

### POST /api/userData
Crear nuevos datos personales. Requiere rol Developer.

**Request Body:**
```json
{
    "user_id": "integer (required, exists:users,id, unique)",
    "num_phone": "string (optional, max:20)",
    "num_phone_alt": "string (optional, max:20)",
    "num_identification": "string (optional, max:50, unique)",
    "identification_type": "string (optional, max:45)",
    "address": "string (optional, max:45)",
    "emergency_contact": "string (optional, max:100)",
    "emergency_phone": "string (optional, max:25)"
}
```

**Response (201):**
```json
{
    "status": true,
    "message": "Datos de usuario creados exitosamente"
}
```

**Errores (422):**
```json
{
    "status": false,
    "message": "Error de validación",
    "errors": {
        "user_id": ["El usuario ya tiene datos personales registrados"],
        "num_identification": ["El número de identificación ya está en uso"]
    }
}
```

### GET /api/userData/{user_id}
Obtener datos personales de un usuario específico. Requiere rol Developer.

**Response (201):**
```json
{
    "status": true,
    "message": "Datos de usuario encontrados",
    "data": {
        "id": 1,
        "user_id": 1,
        "num_phone": "1234567890",
        "num_phone_alt": "0987654321",
        "num_identification": "12345678",
        "identification_type": "CC",
        "address": "Calle 123 #45-67",
        "emergency_contact": "María García",
        "emergency_phone": "1111111111"
    }
}
```

**No encontrado (404):**
```json
{
    "message": "No se encuentran datos del usuario"
}
```

### PUT /api/userData/{user_id}
Actualizar datos personales de un usuario. Requiere rol Developer.

**Request Body:** (Mismo formato que POST, sin user_id)

**Response (201):**
```json
{
    "status": true,
    "message": "Datos de usuario actualizados exitosamente",
    "data": {
        // datos actualizados
    }
}
```

### DELETE /api/userData/{user_id}
Eliminar datos personales de un usuario. Requiere rol Developer.

**Response (201):**
```json
{
    "status": true,
    "message": "Datos de usuario eliminados exitosamente"
}
```

## Códigos de Estado HTTP

- **200**: OK - Solicitud exitosa
- **201**: Created - Recurso creado exitosamente  
- **401**: Unauthorized - No autenticado o token inválido
- **403**: Forbidden - Sin permisos para acceder al recurso
- **404**: Not Found - Recurso no encontrado
- **422**: Unprocessable Entity - Errores de validación
- **500**: Internal Server Error - Error del servidor

## Manejo de Errores

### Error de Autenticación (401)
```json
{
    "message": "Tu token ha expirado. Por favor, vuelve a iniciar sesión.",
    "success": false
}
```

### Error de Autorización (403)
```json
{
    "message": "No tiene permisos para acceder a este recurso",
    "success": false
}
```

### Error de Validación (422)
```json
{
    "status": false,
    "message": "Error de validación",
    "errors": {
        "field_name": ["Error message 1", "Error message 2"]
    }
}
```

## Middleware y Roles

### Middleware `jwt.auth`
- Verifica que el token JWT sea válido
- Autentica al usuario

### Middleware `role:Developer`
- Requiere que el usuario tenga rol "Developer"
- Acceso completo al sistema

### Middleware `role:Developer,Admin`
- Permite acceso a usuarios con rol "Developer" o "Admin"
- Para endpoints administrativos

## Consideraciones Especiales

1. **Tokens JWT**: Los tokens tienen un tiempo de vida limitado. Usar `/api/refresh` para renovar.

2. **Validación Única**: Campos como `email` y `num_identification` deben ser únicos en el sistema.

3. **Respuestas Consistentes**: Todas las respuestas incluyen un campo `status` o `success` para indicar el resultado.

4. **Manejo de Errores**: Los errores incluyen mensajes descriptivos en español.

5. **Seguridad**: Todas las rutas protegidas requieren autenticación JWT válida.