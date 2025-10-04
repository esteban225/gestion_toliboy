# Ejemplos para probar el FormResponseController

Este documento contiene ejemplos de JSON y comandos para probar los distintos endpoints implementados en el `FormResponseController`.

## Ejemplos de JSON

### 1. Crear una nueva respuesta de formulario (POST /api/form-responses)

```json
{
  "form_id": 1,
  "batch_id": 2,
  "status": "in_progress",
  "values": {
    "nombre_campo_1": "Valor de texto",
    "campo_numerico_1": 42,
    "campo_fecha_1": "2025-10-04",
    "campo_opciones_1": "opcion1",
    "campo_multi_1": ["opcion1", "opcion2"]
  }
}
```

Para probar con archivos, deberás usar una herramienta como Postman o curl que permita enviar datos multipart/form-data.

### 2. Actualizar una respuesta de formulario (PUT /api/form-responses/{id})

```json
{
  "batch_id": 3,
  "status": "completed",
  "values": {
    "nombre_campo_1": "Valor actualizado",
    "campo_numerico_1": 50,
    "campo_fecha_1": "2025-10-05",
    "campo_opciones_1": "opcion2",
    "campo_multi_1": ["opcion2", "opcion3"]
  }
}
```

### 3. Revisar una respuesta de formulario (POST /api/form-responses/{id}/review)

```json
{
  "status": "approved",
  "review_notes": "Respuesta aprobada sin observaciones adicionales."
}
```

O para rechazar:

```json
{
  "status": "rejected",
  "review_notes": "Respuesta rechazada debido a información incompleta en el campo X."
}
```

### 4. Obtener reglas de validación (GET /api/forms/{formId}/validation-rules)

No requiere cuerpo JSON, solo usar el ID del formulario en la URL.

### 5. Obtener respuestas de formulario con filtros (GET /api/form-responses)

Ejemplos de filtrado mediante parámetros de consulta en la URL:

```text
/api/form-responses?form_id=1
/api/form-responses?status=completed
/api/form-responses?user_id=5
/api/form-responses?batch_id=3
/api/form-responses?per_page=20
```

## Comandos para probar con cURL

### 1. Crear una respuesta de formulario

```bash
curl -X POST http://tu-dominio.com/api/form-responses \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer TU_TOKEN_JWT" \
  -d '{
    "form_id": 1,
    "batch_id": 2,
    "status": "in_progress",
    "values": {
      "nombre_campo_1": "Valor de texto",
      "campo_numerico_1": 42,
      "campo_fecha_1": "2025-10-04",
      "campo_opciones_1": "opcion1",
      "campo_multi_1": ["opcion1", "opcion2"]
    }
  }'
```

### 2. Obtener una respuesta específica

```bash
curl -X GET http://tu-dominio.com/api/form-responses/1 \
  -H "Authorization: Bearer TU_TOKEN_JWT"
```

### 3. Actualizar una respuesta

```bash
curl -X PUT http://tu-dominio.com/api/form-responses/1 \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer TU_TOKEN_JWT" \
  -d '{
    "status": "completed",
    "values": {
      "nombre_campo_1": "Valor actualizado",
      "campo_numerico_1": 50
    }
  }'
```

### 4. Revisar una respuesta

```bash
curl -X POST http://tu-dominio.com/api/form-responses/1/review \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer TU_TOKEN_JWT" \
  -d '{
    "status": "approved",
    "review_notes": "Respuesta aprobada sin observaciones adicionales."
  }'
```

### 5. Obtener reglas de validación

```bash
curl -X GET http://tu-dominio.com/api/forms/1/validation-rules \
  -H "Authorization: Bearer TU_TOKEN_JWT"
```

## Consideraciones importantes

1. **Requisitos previos**:  
   - Formularios y campos de formulario ya creados en la base de datos
   - Token JWT válido para autenticación
   - Permisos de usuario adecuados

2. **Valores de formulario**:  
   - Los `field_code` en el JSON de values deben coincidir con los codes de los FormFields del formulario
   - Para campos tipo checkbox o multiselect, los valores deben enviarse como array

3. **Archivos**:  
   - Si trabajas con archivos, usa multipart/form-data en lugar de JSON
   - Los archivos se envían como valores dentro del campo "values"
   - Tamaño máximo de archivo: 10MB
   - Formatos permitidos: jpeg, png, jpg, pdf, doc, docx, xls, xlsx

4. **Estados de respuesta**:  
   - `pending`: Pendiente
   - `in_progress`: En progreso
   - `completed`: Completada
   - `approved`: Aprobada (después de revisión)
   - `rejected`: Rechazada (después de revisión)

5. **Códigos de respuesta HTTP**:  
   - 200: Operación exitosa
   - 201: Creación exitosa
   - 400: Error de validación o solicitud incorrecta
   - 401: No autenticado
   - 404: Recurso no encontrado
   - 500: Error del servidor

## Ejemplos completos para Postman

### Ejemplo de creación con archivo

Para este caso necesitarás:

1. Configurar como `multipart/form-data`
2. Añadir los campos normales como text
3. Añadir los archivos como file

**Campos a configurar:**

- form_id: 1
- batch_id: 2
- status: in_progress
- values[nombre_campo_1]: Valor de texto
- values[campo_numerico_1]: 42
- values[campo_archivo_1]: [SELECCIONAR ARCHIVO]
