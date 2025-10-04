# Flujo de trabajo — Módulo Formularios

Este documento describe, paso a paso, cómo funciona el módulo de Formularios en este proyecto: cómo se crea un formulario y cómo se envía (responde) un formulario. Incluye endpoints sugeridos, payloads de ejemplo, validaciones dinámicas, persistencia y recomendaciones prácticas.

## Resumen de modelos y tablas implicadas
- `forms` → Modelo `App\Models\Form`
- `form_fields` → Modelo `App\Models\FormField`
- `form_responses` → Tabla para respuestas (modelo puede implementarse como `FormResponse`)
- `form_response_values` → Tabla para valores por campo (modelo `FormResponseValue`)

## Arquitectura y flujo general
Cliente → Endpoint HTTP → Request (Validación) → Controller → UseCase → Service → Repository → Eloquent → DB

Las capas actuales en `app/Modules/Forms` son: Controllers, Application/UseCases, Domain/Services, Domain/Entities, Infrastructure/Repositories. Los modelos Eloquent están en `app/Models`.

---

## 1) Crear un formulario

### Endpoint sugerido
- POST /api/forms

### Request (validación)
Usar `FormRegisterRequest` (ya existente) que valida:
- `name` (required|string|max:255)
- `code` (required|string|max:100|unique:forms,code,{form_id})
- `version` (required|string)
- `created_by` (nullable|integer|exists:users,id)
- `is_active` (required|boolean)
- `display_order` (required|integer|min:0|unique:forms,display_order,{form_id})

> Nota: si aceptas `fields` en el payload (crear formulario + campos en una petición), valida también la estructura de `fields` (array) y cada campo internamente.

### Controller
- `FormsController::store(FormRegisterRequest $request)` obtiene `$data = $request->validated()` y hace `$this->useCase->create($data)`.

### UseCase → Service → Repository
- `ManageFormUseCase::create(array $data)` transforma a `FormEntity::fromArray($data)`.
- `FormService::create(FormEntity $entity)` delega a `FormsRepository::create()`.
- `FormsRepository::create(FormEntity $data)` hace `new Form($data->toArray())` y `save()`.

### Recomendaciones al crear con campos (mejor práctica)
Si el payload incluye `fields`, ejecuta dentro de `DB::transaction(...)`:
1. Crear `Form`.
2. Para cada campo en `fields` crear `FormField` asociado (`form_id`).
3. Validar duplicados de `field_code` dentro del mismo payload.
4. Retornar `201 Created` con el recurso creado (idealmente JSON con `id`, `fields`, `created_at`).

### Ejemplo de payload (crear formulario con campos)
```json
{
  "name": "Checklist producción",
  "code": "check_prod",
  "version": "1.0",
  "created_by": 12,
  "is_active": true,
  "display_order": 1,
  "fields": [
    { "label": "Temperatura", "field_code": "temp", "type": "number", "required": true, "validation_rules": {"min":0, "max":200}, "field_order": 1 },
    { "label": "Observaciones", "field_code": "obs", "type": "textarea", "required": false, "field_order": 2 }
  ]
}
```

---

## 2) Enviar (responder) un formulario

### Endpoint sugerido
- POST /api/forms/{form_id}/responses

Soporta `application/json` (valores simples) y `multipart/form-data` cuando hay archivos.

### Validación dinámica (pasos)
1. Cargar `Form` con sus `form_fields`: `Form::with('form_fields')->find($formId)`.
2. Armar reglas Laravel dinámicas por cada campo (`form_fields`) usando `type`, `required` y `validation_rules` (JSON almacenado):
   - `required` → `required`
   - `type`:
     - `text`, `textarea` → `string`
     - `number` → `numeric`
     - `date` → `date`
     - `time` → `date_format:H:i` (si aplica)
     - `select`, `radio`, `checkbox` → `in:...` (usar `options`)
     - `file` → `file|mimes:...|max:...`
   - Añadir reglas extra definidas en `validation_rules` (mergear)
3. Validar que no haya keys en el payload que no correspondan a `field_code` del formulario.

### Controller (sugerido)
- `FormResponseController::store(FormResponseSubmitRequest $request, $formId)`:
  - Obtener `$form = Form::with('form_fields')->findOrFail($formId)`.
  - Construir reglas dinámicas y validar `$request->input('values')` o directamente usar `Validator::make(...)`.
  - Verificar permisos del usuario.

### Persistencia (dentro de transacción)
- `DB::transaction(function() use ($form, $values, $userId) {`
  - Crear `FormResponse` (status `pending` o `in_progress`).
  - Para cada `field` crear `FormResponseValue` con `response_id`, `field_id`, `value` o `file_path`.
  - Si todos OK, marcar `FormResponse.submitted_at = now()` y `status = 'completed'` (si aplica).
- `});`

### Manejo de archivos
- Para `type = file` recibir multipart, validar MIME y tamaño, luego `Storage::disk('s3')->putFile()` o `store()` y guardar `file_path` en `form_response_values`.
- Si falla la transacción, eliminar los archivos subidos.

### Eventos y jobs
- Emitir `FormResponseSubmitted` para notificar revisores y para procesamiento (thumbnails, OCR, PDF) en background.

### Ejemplo payload (sin archivos)
```json
{
  "user_id": 12,
  "batch_id": null,
  "values": {
    "temp": 85.5,
    "obs": "OK en línea 3"
  }
}
```

Si hay archivos, usar `multipart/form-data` y adjuntar `files[field_code]`.

---

## Reglas de negocio y consideraciones
- Validar unicidad de `display_order` en DB y capturar excepción por concurrencia.
- Aceptar guardado parcial: `status = 'in_progress'` y permitir `PATCH` para completar.
- Control de permisos: quién puede crear formularios y quién puede responder (roles/permissions).
- Auditoría: triggers y `audit_logs` ya existen en la DB (revisar que los triggers no rompan operaciones masivas).

## Pruebas recomendadas
- Unit tests: `FormService` (create/update/delete).
- Integration tests: flujo crear formulario + crear campos + recuperar formulario con `form_fields`.
- E2E: submit de respuesta con y sin archivos, validar registros en `form_responses` y `form_response_values`.

## Qué falta implementar (lista priorizada)
1. `FormResponseController`, `FormResponse` model y `FormResponseValue` model (si no existen).
2. UseCase/Service/Repository para respuestas (`ManageFormResponseUseCase`, `FormResponseService`, `FormResponseRepositoryI` + impl).
3. `FormResponseSubmitRequest` con validación básica y/o adaptador para la validación dinámica.
4. Soporte para archivos (Storage config, bucket S3 o disco local con limpieza en rollback).
5. Tests básicos y ejemplos de requests para el frontend.

---

## Próximos pasos que puedo implementar para ti
- Implementar el endpoint `POST /api/forms/{form_id}/responses` completo (controller + usecase + service + repository + request) con validación dinámica y soporte básico de archivos.
- O: Implementar solo la función que transforma `form_fields` a reglas de validación Laravel (útil para usar en controllers).

Dime cuál prefieres y lo implemento.
