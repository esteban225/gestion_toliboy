<?php

namespace App\Http\Controllers;

use App\Models\FormField;
use Illuminate\Http\Request;

/**
 * Controlador para la gestión de campos de formularios.
 * Permite listar, crear, mostrar, actualizar y eliminar campos de formularios.
 */
class FormsFilesController extends Controller
{
    private const ERROR_PROCESSING_REQUEST = 'Ocurrió un error al procesar la solicitud.';
    /**
     * Muestra una lista de todos los campos de formularios.
     *
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con todos los campos o mensaje de error.
     */
    public function index()
    {
        try {
            // Obtiene todos los campos de la base de datos
            $data = FormField::all();

            return response()->json([
                'status' => true,
                'message' => 'Campos obtenidos exitosamente',
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            // Si ocurre una excepción, retorna el error
            return response()->json([
                'message' => self::ERROR_PROCESSING_REQUEST,
                'message' => 'Ocurrió un error al procesar la solicitud.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Muestra el formulario para crear un nuevo campo.
     * (No implementado, normalmente usado en aplicaciones web con vistas)
     *
     * @return void
     */
    public function create()
    {
        //
    }

    /**
     * Almacena un nuevo campo de formulario en la base de datos.
     *
     * @param \Illuminate\Http\Request $request Datos de la solicitud HTTP.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el resultado de la operación.
     */
    public function store(Request $request)
    {
        // Validación de los datos recibidos
        $validatedData = $request->validate([
            'form_id' => 'required|integer',
            'label' => 'required|string|max:255',
            'field_code' => 'required|string|max:100|unique:form_fields,field_code',
            'type' => 'required|string|max:50',
            'required' => 'required|boolean',
            'options' => 'nullable|array',
            'validation_rules' => 'nullable|array',
            'field_order' => 'required|integer',
            'is_active' => 'required|boolean',
        ]);
        try {
            // Crea el campo con los datos validados
            $formField = FormField::create($validatedData);
            return response()->json([
                'status' => true,
                'message' => 'Campo creado exitosamente',
                'data' => $formField
            ], 201);
        } catch (\Exception $e) {
            // Si ocurre una excepción, retorna el error
            return response()->json([
                'message' => self::ERROR_PROCESSING_REQUEST,
                'message' => 'Ocurrió un error al procesar la solicitud.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Muestra la información de un campo específico.
     *
     * @param string $id Identificador del campo.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con los datos o mensaje de error.
     */
    public function show(string $id)
    {
        try {
            // Busca el campo por su ID
            $formField = FormField::find($id);
            if ($formField) {
                // Si existe, retorna los datos
                return response()->json([
                    'status' => true,
                    'message' => 'Campo obtenido exitosamente',
                    'data' => $formField
                ], 200);
            } else {
                // Si no existe, retorna mensaje de error
                return response()->json([
                    'status' => false,
                    'message' => 'Campo no encontrado',
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
     * Muestra el formulario para editar un campo específico.
     * (No implementado)
     *
     * @param string $id Identificador del campo.
     * @return void
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Actualiza la información de un campo específico en la base de datos.
     *
     * @param \Illuminate\Http\Request $request Datos de la solicitud HTTP.
     * @param string $id Identificador del campo.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el resultado de la operación.
     */
    public function update(Request $request, string $id)
    {
        // Validación de los datos recibidos
        $validatedData = $request->validate([
            'form_id' => 'sometimes|required|integer',
            'label' => 'sometimes|required|string|max:255',
            'field_code' => 'sometimes|required|string|max:100|unique:form_fields,field_code,' . $id,
            'type' => 'sometimes|required|string|max:50',
            'required' => 'sometimes|required|boolean',
            'options' => 'nullable|array',
            'validation_rules' => 'nullable|array',
            'field_order' => 'sometimes|required|integer',
            'is_active' => 'sometimes|required|boolean',
        ]);

        try {
            // Busca el campo por su ID
            $formField = FormField::find($id);
            if ($formField) {
                // Actualiza el campo con los datos validados
                $formField->update($validatedData);
                return response()->json([
                    'status' => true,
                    'message' => 'Campo actualizado exitosamente',
                    'data' => $formField
                ], 200);
            } else {
                // Si no existe, retorna mensaje de error
                return response()->json([
                    'status' => false,
                    'message' => 'Campo no encontrado',
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
     * Elimina un campo específico de la base de datos.
     *
     * @param string $id Identificador del campo.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el resultado de la operación.
     */
    public function destroy(string $id)
    {
        try {
            // Busca el campo por su ID
            $formField = FormField::find($id);
            if ($formField) {
                // Elimina el campo
                $formField->delete();
                return response()->json([
                    'status' => true,
                    'message' => 'Campo eliminado exitosamente',
                ], 200);
            } else {
                // Si no existe, retorna mensaje de error
                return response()->json([
                    'status' => false,
                    'message' => 'Campo no encontrado',
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
