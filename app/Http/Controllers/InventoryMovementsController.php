<?php

namespace App\Http\Controllers;

use App\Models\InventoryMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Controlador para la gestión de movimientos de inventario.
 * Permite listar, crear, mostrar, actualizar y eliminar movimientos de inventario.
 */
class InventoryMovementsController extends Controller
{
    /**
     * Muestra una lista de todos los movimientos de inventario.
     *
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con todos los movimientos o mensaje de error.
     */
    public function index()
    {
        try {
            // Obtiene todos los movimientos de inventario de la base de datos
            $data = InventoryMovement::all();
            return response()->json([
                'status' => true,
                'message' => 'Movimientos de inventario obtenidos exitosamente',
                'data' => $data
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
     * Muestra el formulario para crear un nuevo movimiento de inventario.
     * (No implementado, normalmente usado en aplicaciones web con vistas)
     *
     * @return void
     */
    public function create()
    {
        //
    }

    /**
     * Almacena un nuevo movimiento de inventario en la base de datos.
     *
     * @param \Illuminate\Http\Request $request Datos de la solicitud HTTP.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el resultado de la operación.
     */
    public function store(Request $request)
    {
        // Validación de los datos recibidos
        $validator = Validator::make($request->all(), [
            'raw_material_id' => 'required|integer|exists:raw_materials,id',
            'batch_id' => 'nullable|integer|exists:batches,id',
            'movement_type' => 'required|string|in:addition,removal,adjustment',
            'quantity' => 'required|numeric|min:0.01',
            'unit_cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:500',
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
            // Crea el movimiento de inventario con los datos validados
            $movement = InventoryMovement::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Movimiento de inventario creado exitosamente',
                'data' => $movement
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
     * Muestra la información de un movimiento de inventario específico.
     *
     * @param string $id Identificador del movimiento.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con los datos o mensaje de error.
     */
    public function show(string $id)
    {
        try {
            // Busca el movimiento por su ID
            $movement = InventoryMovement::find($id);
            if ($movement) {
                // Si existe, retorna los datos
                return response()->json([
                    'status' => true,
                    'message' => 'Movimiento de inventario obtenido exitosamente',
                    'data' => $movement
                ], 200);
            } else {
                // Si no existe, retorna mensaje de error
                return response()->json([
                    'status' => false,
                    'message' => 'Movimiento de inventario no encontrado',
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
     * Muestra el formulario para editar un movimiento de inventario específico.
     * (No implementado)
     *
     * @param string $id Identificador del movimiento.
     * @return void
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Actualiza la información de un movimiento de inventario específico en la base de datos.
     *
     * @param \Illuminate\Http\Request $request Datos de la solicitud HTTP.
     * @param string $id Identificador del movimiento.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el resultado de la operación.
     */
    public function update(Request $request, string $id)
    {
        try {
            // Busca el movimiento por su ID
            $movement = InventoryMovement::find($id);
            if (!$movement) {
                // Si no existe, retorna mensaje de error
                return response()->json([
                    'status' => false,
                    'message' => 'Movimiento de inventario no encontrado',
                ], 404);
            }

            // Validación de los datos recibidos
            $validator = Validator::make($request->all(), [
                'raw_material_id' => 'sometimes|required|integer|exists:raw_materials,id',
                'batch_id' => 'sometimes|nullable|integer|exists:batches,id',
                'movement_type' => 'sometimes|required|string|in:addition,removal,adjustment',
                'quantity' => 'sometimes|required|numeric|min:0.01',
                'unit_cost' => 'sometimes|nullable|numeric|min:0',
                'notes' => 'sometimes|nullable|string|max:500',
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

            // Actualiza el movimiento con los datos validados
            $movement->update($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Movimiento de inventario actualizado exitosamente',
                'data' => $movement
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
     * Elimina un movimiento de inventario específico de la base de datos.
     *
     * @param string $id Identificador del movimiento.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el resultado de la operación.
     */
    public function destroy(string $id)
    {
        try {
            // Busca el movimiento por su ID
            $movement = InventoryMovement::find($id);
            if (!$movement) {
                // Si no existe, retorna mensaje de error
                return response()->json([
                    'status' => false,
                    'message' => 'Movimiento de inventario no encontrado',
                ], 404);
            }

            // Elimina el movimiento
            $movement->delete();
            return response()->json([
                'status' => true,
                'message' => 'Movimiento de inventario eliminado exitosamente',
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
