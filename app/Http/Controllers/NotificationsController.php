<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Controlador para la gestión de notificaciones.
 * Permite listar, crear, mostrar, actualizar y eliminar notificaciones.
 */
class NotificationsController extends Controller
{
    /**
     * Muestra una lista de todas las notificaciones.
     *
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con todas las notificaciones o mensaje de error.
     */
    public function index()
    {
        try{
            // Obtiene todas las notificaciones de la base de datos
            $data = Notification::all();
            return response()->json([
                'status' => true,
                'message' => 'Notificaciones obtenidas exitosamente',
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
     * Muestra el formulario para crear una nueva notificación.
     * (No implementado, normalmente usado en aplicaciones web con vistas)
     *
     * @return void
     */
    public function create()
    {
        //
    }

    /**
     * Almacena una nueva notificación en la base de datos.
     *
     * @param \Illuminate\Http\Request $request Datos de la solicitud HTTP.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el resultado de la operación.
     */
    public function store(Request $request)
    {
        try{
            // Validación de los datos recibidos
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'message' => 'required|string',
                'user_id' => 'required|integer|exists:users,id',
            ]);
            // Si la validación falla, retorna los errores
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Crea la notificación con los datos validados
            $notification = Notification::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Notificación creada exitosamente',
                'data' => $notification
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
     * Muestra la información de una notificación específica.
     *
     * @param string $id Identificador de la notificación.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con los datos o mensaje de error.
     */
    public function show(string $id)
    {
        try{
            // Busca la notificación por su ID
            $notification = Notification::find($id);
            if(!$notification){
                // Si no existe, retorna mensaje de error
                return response()->json([
                    'status' => false,
                    'message' => 'Notificación no encontrada'
                ], 404);
            }
            // Si existe, retorna los datos
            return response()->json([
                'status' => true,
                'message' => 'Notificación obtenida exitosamente',
                'data' => $notification
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
     * Muestra el formulario para editar una notificación específica.
     * (No implementado)
     *
     * @param string $id Identificador de la notificación.
     * @return void
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Actualiza la información de una notificación específica en la base de datos.
     *
     * @param \Illuminate\Http\Request $request Datos de la solicitud HTTP.
     * @param string $id Identificador de la notificación.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el resultado de la operación.
     */
    public function update(Request $request, string $id)
    {
        try{
            // Busca la notificación por su ID
            $notification = Notification::find($id);
            if(!$notification){
                // Si no existe, retorna mensaje de error
                return response()->json([
                    'status' => false,
                    'message' => 'Notificación no encontrada'
                ], 404);
            }

            // Validación de los datos recibidos
            $validator = Validator::make($request->all(), [
                'title' => 'sometimes|required|string|max:255',
                'message' => 'sometimes|required|string',
                'user_id' => 'sometimes|required|integer|exists:users,id',
            ]);
            // Si la validación falla, retorna los errores
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Actualiza la notificación con los datos validados
            $notification->update($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Notificación actualizada exitosamente',
                'data' => $notification
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
     * Elimina una notificación específica de la base de datos.
     *
     * @param string $id Identificador de la notificación.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el resultado de la operación.
     */
    public function destroy(string $id)
    {
        try{
            // Busca la notificación por su ID
            $notification = Notification::find($id);
            if(!$notification){
                // Si no existe, retorna mensaje de error
                return response()->json([
                    'status' => false,
                    'message' => 'Notificación no encontrada'
                ], 404);
            }
            // Elimina la notificación
            $notification->delete();
            return response()->json([
                'status' => true,
                'message' => 'Notificación eliminada exitosamente'
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
