<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Controlador para la gestión de lotes de producción.
 * Permite listar, crear, mostrar, actualizar y eliminar lotes.
 */
class BatchesController extends Controller
{
    /**
     * Muestra una lista de todos los lotes.
     *
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con todos los lotes o mensaje de error.
     */
    public function index()
    {
        try {
            // Obtiene todos los lotes de la base de datos
            $batches = Batch::all();

            if ($batches->isEmpty()) {
                // Si no hay lotes registrados
                return response()->json(['message' => 'No se encuentran lotes'], 404);
            } else {
                // Si existen lotes, retorna los datos
                return response()->json([
                    'status' => true,
                    'message' => 'Lotes encontrados',
                    'data' => $batches
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
     * Muestra el formulario para crear un nuevo lote.
     * (No implementado, normalmente usado en aplicaciones web con vistas)
     *
     * @return void
     */
    public function create()
    {

    }

    /**
     * Almacena un nuevo lote en la base de datos.
     *
     * @param \Illuminate\Http\Request $request Datos de la solicitud HTTP.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el resultado de la operación.
     */
    public function store(Request $request)
    {
        // Validación de los datos recibidos
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:100|unique:batches,code',
            'product_id' => 'nullable|integer|exists:products,id',
            'start_date' => 'required|date',
            'expected_end_date' => 'nullable|date|after_or_equal:start_date',
            'actual_end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|string|max:50',
            'quantity' => 'required|integer|min:1',
            'defect_quantity' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
            'created_by' => 'nullable|integer|exists:users,id'
        ]);
        // Si la validación falla, retorna los errores
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Crea el lote con los datos validados
            $batch = Batch::create($request->all());

            return response()->json([
                'status' => true,
                'message' => 'Lote creado exitosamente',
                'data' => $batch
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
     * Muestra la información de un lote específico.
     *
     * @param string $id Identificador del lote.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con los datos o mensaje de error.
     */
    public function show(string $id)
    {
        try {
            // Busca el lote por su ID
            $batch = Batch::find($id);
            if ($batch) {
                // Si existe, retorna los datos
                return response()->json([
                    'status' => true,
                    'message' => 'Lote encontrado',
                    'data' => $batch
                ], 200);
            } else {
                // Si no existe, retorna mensaje de error
                return response()->json([
                    'status' => false,
                    'message' => 'Lote no encontrado'
                ], 404);
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
     * Muestra el formulario para editar un lote específico.
     * (No implementado)
     *
     * @param string $id Identificador del lote.
     * @return void
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Actualiza la información de un lote específico en la base de datos.
     *
     * @param \Illuminate\Http\Request $request Datos de la solicitud HTTP.
     * @param string $id Identificador del lote.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el resultado de la operación.
     */
    public function update(Request $request, string $id)
    {
        // Validación de los datos recibidos
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'code' => 'sometimes|required|string|max:100|unique:batches,code,' . $id,
            'product_id' => 'sometimes|nullable|integer|exists:products,id',
            'start_date' => 'sometimes|required|date',
            'expected_end_date' => 'sometimes|nullable|date|after_or_equal:start_date',
            'actual_end_date' => 'sometimes|nullable|date|after_or_equal:start_date',
            'status' => 'sometimes|required|string|max:50',
            'quantity' => 'sometimes|required|integer|min:1',
            'defect_quantity' => 'sometimes|nullable|integer|min:0',
            'notes' => 'sometimes|nullable|string',
            'created_by' => 'sometimes|nullable|integer|exists:users,id'
        ]);
        // Si la validación falla, retorna los errores
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Busca el lote por su ID
            $batch = Batch::find($id);
            if ($batch) {
                // Actualiza el lote con los datos validados
                $batch->update($request->all());
                return response()->json([
                    'status' => true,
                    'message' => 'Lote actualizado exitosamente',
                    'data' => $batch
                ], 200);
            } else {
                // Si no existe, retorna mensaje de error
                return response()->json([
                    'status' => false,
                    'message' => 'Lote no encontrado'
                ], 404);
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
     * Elimina un lote específico de la base de datos.
     *
     * @param string $id Identificador del lote.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el resultado de la operación.
     */
    public function destroy(string $id)
    {
        try {
            // Busca el lote por su ID
            $batch = Batch::find($id);
            if ($batch) {
                // Elimina el lote
                $batch->delete();
                return response()->json([
                    'status' => true,
                    'message' => 'Lote eliminado exitosamente'
                ], 200);
            } else {
                // Si no existe, retorna mensaje de error
                return response()->json([
                    'status' => false,
                    'message' => 'Lote no encontrado'
                ], 404);
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
}
