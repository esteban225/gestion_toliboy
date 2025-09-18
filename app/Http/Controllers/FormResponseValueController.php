<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFormResponseRequest;
use App\Models\FormResponseValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * Controlador para la gestión de valores de respuestas de formularios.
 * Permite listar, crear, mostrar, actualizar y eliminar valores de respuestas.
 */
class FormResponseValueController extends Controller
{
    /**
     * Muestra una lista de todos los valores de respuestas de formularios.
     */
    public function index()
    {
        try {
            $data = FormResponseValue::all();
            if ($data->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No se encuentran valores de respuestas de formularios'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Valores de respuestas de formularios encontrados',
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Ocurrió un error al procesar la solicitud.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function create() {}

    /**
     * Almacena un nuevo valor de respuesta en la base de datos.
     */
    public function store(StoreFormResponseRequest $request)
    {
        $userId = Auth::id();

        $data = $request->validated();

        DB::beginTransaction();
        try {
            $responseId = DB::table('form_responses')->insertGetId([
                'form_id' => $data['form_id'],
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $inserts = [];
            foreach ($data['values'] as $v) {
                $inserts[] = [
                    'response_id' => $responseId,
                    'field_id' => $v['field_id'],
                    'value' => isset($v['value']) ? $v['value'] : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if (! empty($inserts)) {
                DB::table('form_response_values')->insert($inserts);
            }

            DB::commit();

            return response()->json([
                'message' => 'Response saved',
                'response_id' => $responseId,
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'Could not save response', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Muestra la información de un valor de respuesta específico.
     */
    public function show(string $id)
    {
        try {
            $formResponseValue = FormResponseValue::find($id);
            if (!$formResponseValue) {
                return response()->json([
                    'status' => false,
                    'message' => 'Valor de respuesta no encontrado'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Valor de respuesta obtenido exitosamente',
                'data' => $formResponseValue
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Ocurrió un error al procesar la solicitud.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function edit(string $id) {}

    /**
     * Actualiza un valor de respuesta.
     */
    public function update(Request $request, string $id)
    {
        try {
            $formResponseValue = FormResponseValue::find($id);
            if (!$formResponseValue) {
                return response()->json([
                    'status' => false,
                    'message' => 'Valor de respuesta no encontrado'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'response_id' => ['sometimes', 'required', 'integer', 'exists:form_responses,id'],
                'field_id' => [
                    'sometimes',
                    'required',
                    'integer',
                    'exists:form_fields,id',
                    function ($attribute, $value, $fail) use ($request, $id) {
                        $current = FormResponseValue::find($id);
                        if ($current) {
                            $exists = FormResponseValue::where('response_id', $request->input('response_id', $current->response_id))
                                ->where('field_id', $value)
                                ->where('id', '!=', $id)
                                ->exists();
                            if ($exists) {
                                $fail('El field_id ya está asociado a la response_id proporcionada.');
                            }
                        }
                    }
                ],
                'value' => ['nullable', 'string'],
                'file_path' => ['nullable', 'string'],
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $formResponseValue->update($request->all());

            return response()->json([
                'status' => true,
                'message' => 'Valor de respuesta actualizado exitosamente',
                'data' => $formResponseValue
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Ocurrió un error al procesar la solicitud.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Elimina un valor de respuesta.
     */
    public function destroy(string $id)
    {
        try {
            $formResponseValue = FormResponseValue::find($id);
            if (!$formResponseValue) {
                return response()->json([
                    'status' => false,
                    'message' => 'Valor de respuesta no encontrado'
                ], 404);
            }

            $formResponseValue->delete();

            return response()->json([
                'status' => true,
                'message' => 'Valor de respuesta eliminado exitosamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Ocurrió un error al procesar la solicitud.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
