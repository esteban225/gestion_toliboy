<?php

namespace App\Http\Controllers;

use App\Models\FormResponseValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Controlador para la gestión de valores de respuestas de formularios.
 * Permite listar, crear, mostrar, actualizar y eliminar valores de respuestas.
 */
class FormResponseValueController extends Controller
{
    /**
     * Muestra una lista de todos los valores de respuestas de formularios.
     *
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con todos los valores o mensaje de error.
     */
    public function index()
    {
        try{
            // Obtiene todos los valores de respuestas
            $data = FormResponseValue::all();
            return response()->json([
                'status' => true,
                'message' => 'Valores de respuestas obtenidos exitosamente',
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
     * Muestra el formulario para crear un nuevo valor de respuesta.
     * (No implementado, normalmente usado en aplicaciones web con vistas)
     *
     * @return void
     */
    public function create()
    {

    }

    /**
     * Almacena un nuevo valor de respuesta en la base de datos.
     *
     * @param \Illuminate\Http\Request $request Datos de la solicitud HTTP.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el resultado de la operación.
     */
    public function store(Request $request)
    {
        try{
            // Validación de los datos recibidos
            $validator = Validator::make($request->all(), [
                'response_id' => 'required|exists:form_responses,id',
                'field_id' => ['required|integer|exists:form_fields,id',
                    // Validación personalizada para evitar duplicados
                    function ($attribute, $value, $fail) use ($request) {
                        $exists = FormResponseValue::where('response_id', $request->input('response_id'))
                            ->where('field_id', $value)
                            ->exists();
                        if ($exists) {
                            $fail('El campo_id ya está asociado a la response_id proporcionada.');
                        }
                    }
                ],
                'value' => 'nullable|string',
                'file_path' => 'nullable|string'
            ]);
            // Si la validación falla, retorna los errores
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Crea el valor de respuesta con los datos validados
            $formResponseValue = FormResponseValue::create($request->all());

            return response()->json([
                'status' => true,
                'message' => 'Valor de respuesta creado exitosamente',
                'data' => $formResponseValue
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
     * Muestra la información de un valor de respuesta específico.
     *
     * @param string $id Identificador del valor de respuesta.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con los datos o mensaje de error.
     */
    public function show(string $id)
    {
        try{
            // Busca el valor de respuesta por su ID
            $formResponseValue = FormResponseValue::find($id);
            if(!$formResponseValue){
                // Si no existe, retorna mensaje de error
                return response()->json([
                    'status' => false,
                    'message' => 'Valor de respuesta no encontrado'
                ], 404);
            }
            // Si existe, retorna los datos
            return response()->json([
                'status' => true,
                'message' => 'Valor de respuesta obtenido exitosamente',
                'data' => $formResponseValue
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
     * Muestra el formulario para editar un valor de respuesta específico.
     * (No implementado)
     *
     * @param string $id Identificador del valor de respuesta.
     * @return void
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Actualiza la información de un valor de respuesta específico en la base de datos.
     *
     * @param \Illuminate\Http\Request $request Datos de la solicitud HTTP.
     * @param string $id Identificador del valor de respuesta.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el resultado de la operación.
     */
    public function update(Request $request, string $id)
    {
        try{
            // Busca el valor de respuesta por su ID
            $formResponseValue = FormResponseValue::find($id);
            if(!$formResponseValue){
                // Si no existe, retorna mensaje de error
                return response()->json([
                    'status' => false,
                    'message' => 'Valor de respuesta no encontrado'
                ], 404);
            }

            // Validación de los datos recibidos
            $validator = Validator::make($request->all(), [
                'response_id' => 'sometimes|required|integer',
                'field_id' => ['sometimes|required|integer|exists:form_fields,id',
                    // Validación personalizada para evitar duplicados
                    function ($attribute, $value, $fail) use ($request, $id) {
                        $formResponseValue = FormResponseValue::find($id);
                        if ($formResponseValue) {
                            $exists = FormResponseValue::where('response_id', $request->input('response_id', $formResponseValue->response_id))
                                ->where('field_id', $value)
                                ->where('id', '!=', $id)
                                ->exists();
                            if ($exists) {
                                $fail('El campo_id ya está asociado a la response_id proporcionada.');
                            }
                        }
                    }
                ],
                'value' => 'nullable|string',
                'file_path' => 'nullable|string'
            ]);
            // Si la validación falla, retorna los errores
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Actualiza el valor de respuesta con los datos validados
            $formResponseValue->update($request->all());

            return response()->json([
                'status' => true,
                'message' => 'Valor de respuesta actualizado exitosamente',
                'data' => $formResponseValue
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
     * Elimina un valor de respuesta específico de la base de datos.
     *
     * @param string $id Identificador del valor de respuesta.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el resultado de la operación.
     */
    public function destroy(string $id)
    {
        try{
            // Busca el valor de respuesta por su ID
            $formResponseValue = FormResponseValue::find($id);
            if(!$formResponseValue){
                // Si no existe, retorna mensaje de error
                return response()->json([
                    'status' => false,
                    'message' => 'Valor de respuesta no encontrado'
                ], 404);
            }

            // Elimina el valor de respuesta
            $formResponseValue->delete();

            return response()->json([
                'status' => true,
                'message' => 'Valor de respuesta eliminado exitosamente'
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
