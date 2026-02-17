<?php

namespace App\Modules\Forms\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\FormResponse;
use App\Modules\Forms\Application\UseCases\ManageFormResponseUseCase;
use App\Modules\Forms\Application\UseCases\SubmitFormResponseUseCase;
use App\Modules\Forms\Http\Requests\FormResponseFilterRequest;
use App\Modules\Forms\Http\Requests\FormResponseStoreRequest;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * Controlador para la gestión de respuestas de formularios.
 * Permite listar, crear, mostrar, actualizar y eliminar respuestas de formularios.
 */
#[Group(name: 'Módulo de Formularios: Gestión de respuestas', weight: 11)]
class FormResponseController extends Controller
{
    public function __construct(
        private ManageFormResponseUseCase $useCase,
        private SubmitFormResponseUseCase $submitFormResponseUseCase
    ) {
        $this->useCase = $useCase;
        $this->submitFormResponseUseCase = $submitFormResponseUseCase;
    }

    /**
     * Listar todas las respuestas de formularios.
     *
     * Muestra una lista de todas las respuestas de formularios con filtros y paginación.
     * Este endpoint permite obtener todas las respuestas de formularios del sistema de manera paginada.
     * Soporta filtrado por diferentes campos y permite personalizar la cantidad de resultados por página.
     * La respuesta incluye metadatos de paginación y la información detallada de cada respuesta.
     *
     * @param  \App\Modules\Forms\Http\Requests\FormResponseFilterRequest  $request  Datos de la solicitud HTTP que incluyen filtros y parámetros de paginación.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con todas las respuestas o mensaje de error.
     */
    public function index(FormResponseFilterRequest $request)
    {
        try {
            $filters = $request->except(['page', 'per_page']);
            $perpage = $request->input('per_page', 15);

            $query = FormResponse::with([
                'user:id,name',
                'form:id,name,code',
                'form_response_values.form_field',
                'batch:id,name,code',
            ]);

            // Aplicar filtros si los tienes definidos
            foreach ($filters as $key => $value) {
                if (!empty($value)) {
                    $query->where($key, $value);
                }
            }

            $paginator = $query->paginate($perpage);

            if ($paginator->isEmpty()) {
                return response()->json(['message' => 'No se encontraron respuestas de formularios'], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Respuestas de formularios recuperadas con éxito',
                'data' => $paginator->items(),
                'meta' => [
                    'current_page' => $paginator->currentPage(),
                    'last_page' => $paginator->lastPage(),
                    'per_page' => $paginator->perPage(),
                    'total' => $paginator->total(),
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Ocurrió un error al procesar la solicitud.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Muestra el formulario para crear una nueva respuesta.
     * (No implementado, normalmente usado en aplicaciones web con vistas)
     *
     * @return void
     */
    public function create() {}

    /**
     * Almacena una nueva respuesta de formulario en la base de datos.
     *
     * Este endpoint permite crear una nueva respuesta para un formulario específico.
     * Valida que el formulario esté activo antes de procesar la respuesta.
     * Procesa y almacena todos los valores de los campos del formulario, incluyendo archivos adjuntos si los hay.
     * Realiza validaciones específicas según el tipo de campo y las reglas definidas en el formulario.
     *
     * @param  App\Modules\Forms\Http\Requests\FormResponseStoreRequest  $request  Datos de la solicitud HTTP que contienen los valores de los campos del formulario.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el resultado de la operación y los datos de la respuesta creada.
     */
    public function store(FormResponseStoreRequest $request)
    {
        try {
            Log::info('Almacenando nueva respuesta de formulario', ['data' => $request->all()]);
            $form = $this->submitFormResponseUseCase->execute($request->validated());

            // Verificamos que el formulario esté activo
            if (! $form) {
                return response()->json([
                    'status' => false,
                    'message' => 'El formulario no está activo',
                ], 400);
            }

            return response()->json(
                [
                    'status' => $form['status'],
                    'message' => $form['message'],
                    'data' => $form['data'],
                ]
            );

        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    /**
     * Muestra la información de una respuesta de formulario específica.
     *
     * Este endpoint recupera los detalles completos de una respuesta de formulario incluyendo:
     *
     * - Información del usuario que completó el formulario
     * - Detalles del formulario (nombre y código)
     * - Valores de todos los campos respondidos
     * - Información del lote asociado si existe
     *
     * La respuesta incluye todas las relaciones necesarias para mostrar la información completa.
     *
     * @param  string  $id  Identificador único de la respuesta de formulario.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con los datos detallados o mensaje de error si no se encuentra.
     */
    public function show(string $id)
    {
        try {
            $formResponse = FormResponse::with([
                'user:id,name',
                'form:id,name,code',
                'form_response_values.form_field',
                'batch:id,name,code',
            ])->find($id);

            if (! $formResponse) {
                return response()->json(['message' => 'Respuesta de formulario no encontrada'], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Respuesta de formulario encontrada',
                'data' => $formResponse,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Ocurrió un error al procesar la solicitud.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Muestra el formulario para editar una respuesta específica.
     * (No implementado)
     *
     * @param  string  $id  Identificador de la respuesta.
     * @return void
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Actualiza la información de una respuesta de formulario específica en la base de datos.
     *
     * Este endpoint permite modificar una respuesta existente con las siguientes características:
     *
     * - Solo permite actualizar respuestas en estado 'pending' o 'in_progress'
     * - Puede actualizar el lote asociado a la respuesta
     * - Permite modificar los valores de los campos existentes
     * - Maneja la actualización de archivos adjuntos, eliminando los anteriores si es necesario
     * - Actualiza el estado de la respuesta y la fecha de envío si se marca como completada
     * - Realiza todas las operaciones dentro de una transacción para garantizar la integridad
     *
     * @param  \Illuminate\Http\Request  $request  Datos de la solicitud HTTP con los nuevos valores y estado.
     * @param  string  $id  Identificador único de la respuesta a actualizar.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el resultado de la operación y los datos actualizados.
     */
    public function update(Request $request, string $id)
    {
        try {
            // Validación básica
            $request->validate([
                'batch_id' => 'nullable|exists:batches,id',
                'values' => 'required|array',
                'status' => 'nullable|in:pending,in_progress,completed',
            ]);

            // Obtener la respuesta existente
            $formResponse = FormResponse::findOrFail($id);

            // Solo permitir actualizar si está en progreso o pendiente
            if (! in_array($formResponse->status, ['pending', 'in_progress'])) {
                return response()->json([
                    'status' => false,
                    'message' => 'No se puede actualizar una respuesta que ya está completada o revisada',
                ], 400);
            }

            // Obtener el formulario con sus campos
            $form = \App\Models\Form::with('form_fields')->findOrFail($formResponse->form_id);

            // Validar los valores del formulario
            $this->validateFormValues($form, $request->values);

            \Illuminate\Support\Facades\DB::beginTransaction();
            try {
                // Actualizar la respuesta del formulario
                if ($request->has('batch_id')) {
                    $formResponse->batch_id = $request->batch_id;
                }

                if ($request->has('status')) {
                    $formResponse->status = $request->status;

                    // Si el estado es completado, establecemos la fecha de envío
                    if ($request->status === 'completed' && ! $formResponse->submitted_at) {
                        $formResponse->submitted_at = now();
                    }
                }

                $formResponse->save();

                // Actualizar los valores de los campos
                foreach ($request->values as $fieldCode => $value) {
                    // Encontrar el campo por su código
                    $field = $form->form_fields->firstWhere('field_code', $fieldCode);

                    if (! $field) {
                        continue;
                    }

                    // Buscar si ya existe un valor para este campo
                    $responseValue = $formResponse->form_response_values()
                        ->where('field_id', $field->id)
                        ->first();

                    // Si es un archivo, lo procesamos
                    if ($field->type === 'file' && $request->hasFile("values.{$fieldCode}")) {
                        $file = $request->file("values.{$fieldCode}");
                        $path = $file->store('form_responses/'.$formResponse->id, 'public');

                        // Si ya existe un valor para este campo, eliminar el archivo anterior y actualizar
                        if ($responseValue) {
                            if ($responseValue->file_path) {
                                \Illuminate\Support\Facades\Storage::disk('public')->delete($responseValue->file_path);
                            }

                            $responseValue->update([
                                'value' => $file->getClientOriginalName(),
                                'file_path' => $path,
                            ]);
                        } else {
                            // Si no existe, crear un nuevo valor
                            $formResponse->form_response_values()->create([
                                'field_id' => $field->id,
                                'value' => $file->getClientOriginalName(),
                                'file_path' => $path,
                            ]);
                        }
                    } else {
                        // Para otros tipos de campos
                        $fieldValue = is_array($value) ? json_encode($value) : $value;

                        if ($responseValue) {
                            $responseValue->update([
                                'value' => $fieldValue,
                            ]);
                        } else {
                            $formResponse->form_response_values()->create([
                                'field_id' => $field->id,
                                'value' => $fieldValue,
                            ]);
                        }
                    }
                }

                \Illuminate\Support\Facades\DB::commit();

                // Cargamos las relaciones necesarias
                $formResponse->load(['user:id,name', 'form:id,name,code', 'form_response_values.form_field']);

                return response()->json([
                    'status' => true,
                    'message' => 'Respuesta de formulario actualizada exitosamente',
                    'data' => $formResponse,
                ], 200);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\DB::rollback();

                return response()->json([
                    'status' => false,
                    'message' => 'Error al actualizar la respuesta del formulario',
                    'error' => $e->getMessage(),
                ], 500);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Ocurrió un error al procesar la solicitud.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Elimina una respuesta de formulario específica de la base de datos.
     *
     * Este endpoint permite eliminar permanentemente una respuesta de formulario del sistema.
     *
     * La operación es irreversible y elimina:
     *
     * - La respuesta principal del formulario
     * - Todos los valores asociados a los campos
     * - Los archivos adjuntos almacenados (si existen)
     *
     * Verifica la existencia de la respuesta antes de intentar eliminarla.
     *
     * @param  string  $id  Identificador único de la respuesta a eliminar.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON confirmando la eliminación o mensaje de error.
     */
    public function destroy(string $id)
    {
        try {
            $formResponse = FormResponse::find($id);
            if (! $formResponse) {
                // Si no existe, retorna mensaje de error
                return response()->json(['message' => 'Respuesta de formulario no encontrada'], 404);
            }

            // Elimina la respuesta
            $formResponse->delete();

            return response()->json([
                'status' => true,
                'message' => 'Respuesta de formulario eliminada exitosamente',
            ], 200);
        } catch (\Exception $e) {
            // Si ocurre una excepción, retorna el error
            return response()->json([
                'status' => false,
                'message' => 'Ocurrió un error al procesar la solicitud.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Revisar una respuesta de formulario enviada.
     *
     * Este endpoint permite a los revisores aprobar o rechazar una respuesta de formulario.
     * Solo se pueden revisar respuestas que estén en estado 'completed'.
     *
     * El proceso de revisión incluye:
     *
     * - Actualización del estado a 'approved' o 'rejected'
     * - Registro del usuario que realizó la revisión
     * - Timestamp de la revisión
     * - Almacenamiento de notas o comentarios de la revisión
     *
     * @param  Request  $request  Datos de la revisión incluyendo estado y notas
     * @param  int  $id  Identificador único de la respuesta a revisar
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el resultado de la revisión
     */
    public function review(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|in:in_progress,approved,rejected,rejected',
                'review_notes' => 'nullable|string|max:1000',
            ]);

            $formResponse = FormResponse::findOrFail($id);

            // Solo se pueden revisar respuestas completadas
            if ($formResponse->status !== 'completed') {
                return response()->json([
                    'status' => false,
                    'message' => 'No se puede revisar una respuesta que no está completada',
                ], 400);
            }

            $formResponse->update([
                'status' => $request->status,
                'reviewed_by' => \Illuminate\Support\Facades\Auth::id(),
                'reviewed_at' => now(),
                'review_notes' => $request->review_notes,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Respuesta de formulario revisada exitosamente',
                'data' => $formResponse->load(['user:id,name', 'form:id,name,code']),
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Ocurrió un error al procesar la solicitud.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Validar valores de formulario según las reglas de campos de formulario.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateFormValues(\App\Models\Form $form, array $values)
    {
        $validationRules = [];
        $messages = [];

        // Construir reglas de validación basadas en los campos del formulario
        foreach ($form->form_fields as $field) {
            // Solo validamos campos activos
            if (! $field->is_active) {
                continue;
            }

            $rules = [];

            // Si el campo es requerido
            if ($field->required) {
                if ($field->type === 'file') {
                    $rules[] = 'required_without:id';
                } else {
                    $rules[] = 'required';
                }
            } else {
                $rules[] = 'nullable';
            }

            // Reglas basadas en el tipo de campo
            switch ($field->type) {
                case 'text':
                case 'textarea':
                    $rules[] = 'string';
                    break;

                case 'number':
                    $rules[] = 'numeric';
                    break;

                case 'date':
                    $rules[] = 'date';
                    break;

                case 'time':
                    $rules[] = 'date_format:H:i';
                    break;

                case 'datetime':
                    $rules[] = 'date_format:Y-m-d H:i:s';
                    break;

                case 'select':
                case 'radio':
                    if (! empty($field->options)) {
                        $options = is_array($field->options)
                            ? $field->options
                            : json_decode($field->options, true);

                        if (is_array($options)) {
                            $allowed = array_map(function ($item) {
                                return is_array($item) && isset($item['value'])
                                    ? $item['value']
                                    : $item;
                            }, $options);

                            $rules[] = 'in:'.implode(',', $allowed);
                        }
                    }
                    break;

                case 'checkbox':
                case 'multiselect':
                    // El campo debe ser un array (puede tener varios valores)
                    $rules[] = 'array';

                    if (! empty($field->options)) {
                        $options = is_array($field->options)
                            ? $field->options
                            : json_decode($field->options, true);

                        if (is_array($options)) {
                            // Extraer los valores permitidos correctamente
                            $allowed = array_map(function ($item) {
                                // Si es un objeto o array con 'value', úsalo
                                if (is_array($item) && isset($item['value'])) {
                                    return $item['value'];
                                }

                                // Si solo es un string (ej: "Huecos"), úsalo tal cual
                                return $item;
                            }, $options);

                            // Agregamos regla para cada elemento del array (campo.*)
                            // Nota: debes conocer el nombre del campo (por ejemplo $field->name)
                            $fieldName = 'values.'.$field->name; // ajusta si tu estructura difiere

                            $validationRules[$fieldName] = ['array'];
                            $validationRules[$fieldName.'.*'] = ['in:'.implode(',', $allowed)];
                        }
                    }
                    break;

                case 'file':
                    if (request()->hasFile("values.{$field->field_code}")) {
                        $rules[] = 'file';
                        $rules[] = 'mimes:jpeg,png,jpg,pdf,doc,docx,xls,xlsx';
                        $rules[] = 'max:10240'; // 10MB
                    }
                    break;
            }

            // Agregar reglas de validación personalizadas si existen
            if (! empty($field->validation_rules)) {
                $customRules = is_array($field->validation_rules) ? $field->validation_rules : json_decode($field->validation_rules, true);
                if (is_array($customRules)) {
                    $rules = array_merge($rules, $customRules);
                }
            }

            if (! empty($rules)) {
                $validationRules["values.{$field->field_code}"] = implode('|', $rules);

                // Agregar mensajes personalizados para este campo
                $messages["values.{$field->field_code}.required"] = "El campo '{$field->label}' es obligatorio.";
                $messages["values.{$field->field_code}.in"] = "El valor seleccionado para '{$field->label}' es inválido.";
                $messages["values.{$field->field_code}.numeric"] = "El campo '{$field->label}' debe ser un número.";
                $messages["values.{$field->field_code}.date"] = "El campo '{$field->label}' debe ser una fecha válida.";
                $messages["values.{$field->field_code}.file"] = "El campo '{$field->label}' debe ser un archivo.";
                $messages["values.{$field->field_code}.mimes"] = "El archivo '{$field->label}' debe ser de tipo: jpeg, png, jpg, pdf, doc, docx, xls, xlsx.";
                $messages["values.{$field->field_code}.max"] = "El archivo '{$field->label}' no debe ser mayor a 10MB.";
            }
        }

        // Realizar la validación
        $validator = Validator::make(['values' => $values], $validationRules, $messages);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }
    }

    /**
     * Obtener reglas de validación para un formulario específico.
     *
     * Este endpoint recupera todas las reglas de validación para un formulario específico.
     *
     * Proporciona información detallada sobre:
     *
     * - Reglas de validación para cada campo
     * - Tipos de campos y sus restricciones
     * - Opciones disponibles para campos select/radio/checkbox
     * - Requisitos de archivos (tipos y tamaños permitidos)
     * - Validaciones personalizadas definidas
     *
     * Útil para validación del lado del cliente antes de enviar respuestas.
     *
     * @param  int  $formId  Identificador único del formulario
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con todas las reglas de validación y metadata del formulario
     */
    public function getValidationRules($formId)
    {
        try {
            $form = \App\Models\Form::with(['form_fields' => function ($query) {
                $query->where('is_active', true)->orderBy('field_order');
            }])->findOrFail($formId);

            $validationRules = [];

            foreach ($form->form_fields as $field) {
                $rules = [];

                // Regla requerido
                if ($field->required) {
                    $rules[] = 'required';
                } else {
                    $rules[] = 'nullable';
                }

                // Reglas basadas en el tipo de campo
                switch ($field->type) {
                    case 'text':
                    case 'textarea':
                        $rules[] = 'string';
                        break;

                    case 'number':
                        $rules[] = 'numeric';
                        break;

                    case 'date':
                        $rules[] = 'date';
                        break;

                    case 'time':
                        $rules[] = 'date_format:H:i';
                        break;

                    case 'datetime':
                        $rules[] = 'date_format:Y-m-d H:i:s';
                        break;

                    case 'select':
                    case 'radio':
                        if (! empty($field->options)) {
                            $options = is_array($field->options) ? $field->options : json_decode($field->options, true);
                            if (is_array($options)) {
                                $rules[] = 'in:'.implode(',', array_column($options, 'value'));
                            }
                        }
                        break;

                    case 'checkbox':
                    case 'multiselect':
                        $rules[] = 'array';
                        if (! empty($field->options)) {
                            $options = is_array($field->options) ? $field->options : json_decode($field->options, true);
                            if (is_array($options)) {
                                $rules[] = 'in:'.implode(',', array_column($options, 'value')).'*';
                            }
                        }
                        break;

                    case 'file':
                        $rules[] = 'file';
                        $rules[] = 'mimes:jpeg,png,jpg,pdf,doc,docx,xls,xlsx';
                        $rules[] = 'max:10240'; // 10MB
                        break;
                }

                // Agregar reglas de validación personalizadas si existen
                if (! empty($field->validation_rules)) {
                    $customRules = is_array($field->validation_rules) ? $field->validation_rules : json_decode($field->validation_rules, true);
                    if (is_array($customRules)) {
                        $rules = array_merge($rules, $customRules);
                    }
                }

                $validationRules[$field->field_code] = [
                    'rules' => $rules,
                    'label' => $field->label,
                    'type' => $field->type,
                    'required' => $field->required,
                    'options' => $field->options,
                ];
            }

            return response()->json([
                'status' => true,
                'message' => 'Reglas de validación obtenidas exitosamente',
                'data' => [
                    'form' => [
                        'id' => $form->id,
                        'name' => $form->name,
                        'code' => $form->code,
                        'description' => $form->description,
                        'version' => $form->version,
                    ],
                    'validation_rules' => $validationRules,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Ocurrió un error al procesar la solicitud.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
