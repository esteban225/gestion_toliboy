<?php

namespace App\Http\Controllers;

use App\Models\WorkLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Controlador para la gestión de registros de trabajo.
 * Permite listar, crear, mostrar, actualizar y eliminar registros de trabajo.
 */
class WorkLogsController extends Controller
{
    /**
     * Muestra una lista de todos los registros de trabajo.
     *
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con todos los registros o mensaje de error.
     */
    public function index()
    {
        try{
            // Obtiene todos los registros de trabajo de la base de datos
            $data = WorkLog::all();
            return response()->json([
                'status' => true,
                'message' => 'Registros de trabajo obtenidos exitosamente',
                'data' => $data
            ], 200);
        }catch(\Exception $e){
            // Si ocurre una excepción, retorna el error
            return response()->json([
                'status' => false,
                'message' => 'Ocurrió un error al procesar la solicitud.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Muestra el formulario para crear un nuevo registro de trabajo.
     * (No implementado, normalmente usado en aplicaciones web con vistas)
     *
     * @return void
     */
    public function create()
    {
        //
    }

    /**
     * Almacena un nuevo registro de trabajo en la base de datos.
     *
     * @param \Illuminate\Http\Request $request Datos de la solicitud HTTP.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el resultado de la operación.
     */
    public function store(Request $request)
    {
        try{
            // Validación de los datos recibidos
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'date' => 'required|date',
                'start_time' => 'required|date',
                'end_time' => 'required|date|after:start_time',
                'description' => 'nullable|string',
                'notes' => 'nullable|string',
            ]);
            // Si la validación falla, retorna los errores
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Crea el registro de trabajo con los datos validados
            $workLog = WorkLog::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Registro de trabajo creado exitosamente',
                'data' => $workLog
            ], 201);
        }catch(\Exception $e){
            // Si ocurre una excepción, retorna el error
            return response()->json([
                'status' => false,
                'message' => 'Ocurrió un error al procesar la solicitud.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Muestra la información de un registro de trabajo específico.
     *
     * @param string $id Identificador del registro de trabajo.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con los datos o mensaje de error.
     */
    public function show(string $id)
    {
        try{
            // Busca el registro de trabajo por su ID
            $workLog = WorkLog::find($id);
            if(!$workLog){
                // Si no existe, retorna mensaje de error
                return response()->json([
                    'status' => false,
                    'message' => 'Registro de trabajo no encontrado'
                ], 404);
            }
            // Si existe, retorna los datos
            return response()->json([
                'status' => true,
                'message' => 'Registro de trabajo obtenido exitosamente',
                'data' => $workLog
            ], 200);
        }catch(\Exception $e){
            // Si ocurre una excepción, retorna el error
            return response()->json([
                'status' => false,
                'message' => 'Ocurrió un error al procesar la solicitud.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Muestra el formulario para editar un registro de trabajo específico.
     * (No implementado)
     *
     * @param string $id Identificador del registro de trabajo.
     * @return void
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Actualiza la información de un registro de trabajo específico en la base de datos.
     *
     * @param \Illuminate\Http\Request $request Datos de la solicitud HTTP.
     * @param string $id Identificador del registro de trabajo.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el resultado de la operación.
     */
    public function update(Request $request, string $id)
    {
        try{
            // Busca el registro de trabajo por su ID
            $workLog = WorkLog::find($id);
            if(!$workLog){
                // Si no existe, retorna mensaje de error
                return response()->json([
                    'status' => false,
                    'message' => 'Registro de trabajo no encontrado'
                ], 404);
            }

            // Validación de los datos recibidos
            $validator = Validator::make($request->all(), [
                'user_id' => 'sometimes|exists:users,id',
                'date' => 'sometimes|date',
                'start_time' => 'sometimes|date',
                'end_time' => 'sometimes|date|after:start_time',
                'description' => 'nullable|string',
                'notes' => 'nullable|string',
            ]);
            // Si la validación falla, retorna los errores
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Actualiza el registro de trabajo con los datos validados
            $workLog->update($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Registro de trabajo actualizado exitosamente',
                'data' => $workLog
            ], 200);
        }catch(\Exception $e){
            // Si ocurre una excepción, retorna el error
            return response()->json([
                'status' => false,
                'message' => 'Ocurrió un error al procesar la solicitud.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Elimina un registro de trabajo específico de la base de datos.
     *
     * @param string $id Identificador del registro de trabajo.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el resultado de la operación.
     */
    public function destroy(string $id)
    {
        try{
            // Busca el registro de trabajo por su ID
            $workLog = WorkLog::find($id);
            if(!$workLog){
                // Si no existe, retorna mensaje de error
                return response()->json([
                    'status' => false,
                    'message' => 'Registro de trabajo no encontrado'
                ], 404);
            }
            // Elimina el registro de trabajo
            $workLog->delete();
            return response()->json([
                'status' => true,
                'message' => 'Registro de trabajo eliminado exitosamente'
            ], 200);
        }catch(\Exception $e){
            // Si ocurre una excepción, retorna el error
            return response()->json([
                'status' => false,
                'message' => 'Ocurrió un error al procesar la solicitud.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
