<?php

namespace App\Http\Controllers;

use App\Models\Form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Controlador para la gestión de formularios.
 * Permite listar, crear, mostrar, actualizar y eliminar formularios.
 */
class FormsController extends Controller
{
    /**
     * Muestra una lista de todos los formularios.
     *
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con todos los formularios o mensaje de error.
     */
    public function index()
    {
        try {
            // Obtiene todos los formularios de la base de datos
            $forms = Form::all();

            if ($forms->isEmpty()) {
                // Si no hay formularios registrados
                return response()->json(['message' => 'No se encuentran formularios'], 404);
            } else {
                // Si existen formularios, retorna los datos
                return response()->json([
                    'status' => true,
                    'message' => 'Formularios encontrados',
                    'data' => $forms,
                ], 200);
            }
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
     * Muestra el formulario para crear un nuevo formulario.
     * (No implementado, normalmente usado en aplicaciones web con vistas)
     *
     * @return void
     */
    public function create()
    {
        //
    }

    /**
     * Almacena un nuevo formulario en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request  Datos de la solicitud HTTP.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el resultado de la operación.
     */
    public function store(Request $request)
    {
        // Validación de los datos recibidos
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:100|unique:forms,code',
            'description' => 'nullable|string',
            'version' => 'required|string|max:50',
            'created_by' => 'nullable|integer|exists:users,id',
            'display_order' => 'required|integer',
            // Agrega aquí otras reglas de validación según los campos del formulario
        ]);
        // Si la validación falla, retorna los errores
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors(),
            ], 422);
        }
        try {
            // Crea el formulario con los datos validados
            $form = Form::create($request->all());

            return response()->json([
                'status' => true,
                'message' => 'Formulario creado exitosamente',
                'data' => $form,
            ], 201);
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
     * Muestra la información de un formulario específico.
     *
     * @param  string  $id  Identificador del formulario.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con los datos o mensaje de error.
     */
    public function show(string $id)
    {
        try {
            // Busca el formulario por su ID
            $form = Form::find($id);
            if (! $form) {
                // Si no existe, retorna mensaje de error
                return response()->json([
                    'status' => false,
                    'message' => 'Formulario no encontrado',
                ], 404);
            }

            // Si existe, retorna los datos
            return response()->json([
                'status' => true,
                'message' => 'Formulario encontrado',
                'data' => $form,
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
     * Muestra el formulario para editar un formulario específico.
     * (No implementado)
     *
     * @param  string  $id  Identificador del formulario.
     * @return void
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Actualiza la información de un formulario específico en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request  Datos de la solicitud HTTP.
     * @param  string  $id  Identificador del formulario.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el resultado de la operación.
     */
    public function update(Request $request, string $id)
    {
        // Validación de los datos recibidos
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'code' => 'sometimes|required|string|max:100|unique:forms,code,'.$id,
            'description' => 'nullable|string',
            'version' => 'sometimes|required|string|max:50',
            'created_by' => 'nullable|integer|exists:users,id',
            'is_active' => 'sometimes|required|boolean',
            'display_order' => 'sometimes|required|integer',
            // Agrega aquí otras reglas de validación según los campos del formulario
        ]);
        // Si la validación falla, retorna los errores
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors(),
            ], 422);
        }
        try {
            // Busca el formulario por su ID
            $form = Form::find($id);
            if (! $form) {
                // Si no existe, retorna mensaje de error
                return response()->json([
                    'status' => false,
                    'message' => 'Formulario no encontrado',
                ], 404);
            }
            // Actualiza el formulario con los datos validados
            $form->update($request->all());

            return response()->json([
                'status' => true,
                'message' => 'Formulario actualizado exitosamente',
                'data' => $form,
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
     * Elimina un formulario específico de la base de datos.
     *
     * @param  string  $id  Identificador del formulario.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el resultado de la operación.
     */
    public function destroy(string $id)
    {
        try {
            // Busca el formulario por su ID
            $form = Form::find($id);
            if (! $form) {
                // Si no existe, retorna mensaje de error
                return response()->json([
                    'status' => false,
                    'message' => 'Formulario no encontrado',
                ], 404);
            }
            // Elimina el formulario
            $form->delete();

            return response()->json([
                'status' => true,
                'message' => 'Formulario eliminado exitosamente',
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
}
