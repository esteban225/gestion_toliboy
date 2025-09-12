<?php

namespace App\Http\Controllers;

use App\Models\FormResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Controlador para la gestión de respuestas de formularios.
 * Permite listar, crear, mostrar, actualizar y eliminar respuestas de formularios.
 */
class FormResponseController extends Controller
{
    /**
     * Muestra una lista de todas las respuestas de formularios.
     *
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con todas las respuestas o mensaje de error.
     */
    public function index()
    {
        try {
            $formResponses = FormResponse::all();
            if ($formResponses->isEmpty()) {
                // Si no hay respuestas registradas
                return response()->json(['message' => 'No se encuentran respuestas de formularios'], 404);
            } else {
                // Si existen respuestas, retorna los datos
                return response()->json([
                    'status' => true,
                    'message' => 'Respuestas de formularios encontradas',
                    'data' => $formResponses
                ], 200);
            }
        } catch (\Exception $e) {
            // Si ocurre una excepción, retorna el error
            return response()->json([
                'status' => false,
                'message' => 'Ocurrió un error al procesar la solicitud.',
                'error' => $e->getMessage()
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
     * @param \Illuminate\Http\Request $request Datos de la solicitud HTTP.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el resultado de la operación.
     */
    public function store(Request $request)
    {
        try {
            // Validación de los datos recibidos
            $validator = Validator::make($request->all(), [
                'form_id' => 'required|integer|exists:forms,id',
                'user_id' => 'required|integer|exists:users,id',
                'batch_id' => 'nullable|integer|exists:batches,id',
                'status' => 'required|string|max:50',
                'submitted_at' => 'nullable|date',
                'reviewed_by' => 'nullable|integer|exists:users,id',
                'reviewed_at' => 'nullable|date',
                'review_notes' => 'nullable|string',
            ]);

            // Si la validación falla, retorna los errores
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Crea la respuesta de formulario con los datos validados
            $formResponse = FormResponse::create($request->all());

            return response()->json([
                'status' => true,
                'message' => 'Respuesta de formulario creada exitosamente',
                'data' => $formResponse
            ], 201);
        } catch (\Exception $e) {
            // Si ocurre una excepción, retorna el error
            return response()->json([
                'status' => false,
                'message' => 'Ocurrió un error al procesar la solicitud.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Muestra la información de una respuesta de formulario específica.
     *
     * @param string $id Identificador de la respuesta.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con los datos o mensaje de error.
     */
    public function show(string $id)
    {
        try {
            $formResponse = FormResponse::find($id);
            if (!$formResponse) {
                // Si no existe, retorna mensaje de error
                return response()->json(['message' => 'Respuesta de formulario no encontrada'], 404);
            } else {
                // Si existe, retorna los datos
                return response()->json([
                    'status' => true,
                    'message' => 'Respuesta de formulario encontrada',
                    'data' => $formResponse
                ], 200);
            }
        } catch (\Exception $e) {
            // Si ocurre una excepción, retorna el error
            return response()->json([
                'status' => false,
                'message' => 'Ocurrió un error al procesar la solicitud.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Muestra el formulario para editar una respuesta específica.
     * (No implementado)
     *
     * @param string $id Identificador de la respuesta.
     * @return void
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Actualiza la información de una respuesta de formulario específica en la base de datos.
     *
     * @param \Illuminate\Http\Request $request Datos de la solicitud HTTP.
     * @param string $id Identificador de la respuesta.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el resultado de la operación.
     */
    public function update(Request $request, string $id)
    {
        try {
            $formResponse = FormResponse::find($id);
            if (!$formResponse) {
                // Si no existe, retorna mensaje de error
                return response()->json(['message' => 'Respuesta de formulario no encontrada'], 404);
            }

            // Validación de los datos recibidos
            $validator = Validator::make($request->all(), [
                'form_id' => 'sometimes|required|integer|exists:forms,id',
                'user_id' => 'sometimes|required|integer|exists:users,id',
                'batch_id' => 'nullable|integer|exists:batches,id',
                'status' => 'sometimes|required|string|max:50',
                'submitted_at' => 'nullable|date',
                'reviewed_by' => 'nullable|integer|exists:users,id',
                'reviewed_at' => 'nullable|date',
                'review_notes' => 'nullable|string',
            ]);

            // Si la validación falla, retorna los errores
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Actualiza la respuesta con los datos validados
            $formResponse->update($request->all());

            return response()->json([
                'status' => true,
                'message' => 'Respuesta de formulario actualizada exitosamente',
                'data' => $formResponse
            ], 200);
        } catch (\Exception $e) {
            // Si ocurre una excepción, retorna el error
            return response()->json([
                'status' => false,
                'message' => 'Ocurrió un error al procesar la solicitud.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Elimina una respuesta de formulario específica de la base de datos.
     *
     * @param string $id Identificador de la respuesta.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el resultado de la operación.
     */
    public function destroy(string $id)
    {
        try {
            $formResponse = FormResponse::find($id);
            if (!$formResponse) {
                // Si no existe, retorna mensaje de error
                return response()->json(['message' => 'Respuesta de formulario no encontrada'], 404);
            }

            // Elimina la respuesta
            $formResponse->delete();

            return response()->json([
                'status' => true,
                'message' => 'Respuesta de formulario eliminada exitosamente'
            ], 200);
        } catch (\Exception $e) {
            // Si ocurre una excepción, retorna el error
            return response()->json([
                'status' => false,
                'message' => 'Ocurrió un error al procesar la solicitud.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
