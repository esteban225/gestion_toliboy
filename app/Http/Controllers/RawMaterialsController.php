<?php

namespace App\Http\Controllers;

use App\Models\RawMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Controlador para la gestión de materias primas.
 * Permite listar, crear, mostrar, actualizar y eliminar materias primas.
 */
class RawMaterialsController extends Controller
{
    /**
     * Muestra una lista de todas las materias primas.
     *
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con todas las materias primas o mensaje de error.
     */
    public function index()
    {
        try {
            // Obtiene todas las materias primas de la base de datos
            $rawMaterials = RawMaterial::all();
            if ($rawMaterials->isEmpty()) {
                // Si no hay materias primas registradas
                return response()->json(['message' => 'No se encuentran materias primas'], 404);
            } else {
                // Si existen materias primas, retorna los datos
                return response()->json([
                    'status' => true,
                    'message' => 'Materias primas encontradas',
                    'data' => $rawMaterials,
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
     * Muestra el formulario para crear una nueva materia prima.
     * (No implementado, normalmente usado en aplicaciones web con vistas)
     *
     * @return void
     */
    public function create()
    {
        //
    }

    /**
     * Almacena una nueva materia prima en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request  Datos de la solicitud HTTP.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el resultado de la operación.
     */
    public function store(Request $request)
    {
        // Validación de los datos recibidos
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:100|unique:raw_materials,code',
            'description' => 'nullable|string',
            'unit_of_measure' => 'required|string|max:50',
            'stock' => 'required|numeric|min:0',
            'min_stock' => 'nullable|numeric|min:0',
            'is_active' => 'required|boolean',
            'created_by' => 'nullable|integer|exists:users,id',
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
            // Crea la materia prima con los datos validados
            $rawMaterial = RawMaterial::create($request->all());
            if ($rawMaterial) {
                // Si se creó correctamente
                return response()->json([
                    'status' => true,
                    'message' => 'Materia prima creada exitosamente',
                    'data' => $rawMaterial,
                ], 201);
            } else {
                // Si no se pudo crear el registro
                return response()->json([
                    'status' => false,
                    'message' => 'No se pudo crear el registro',
                ], 500);
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
     * Muestra la información de una materia prima específica.
     *
     * @param  string  $id  Identificador de la materia prima.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con los datos o mensaje de error.
     */
    public function show(string $id)
    {
        try {
            // Busca la materia prima por su ID
            $rawMaterial = RawMaterial::find($id);
            if ($rawMaterial) {
                // Si existe, retorna los datos
                return response()->json([
                    'status' => true,
                    'message' => 'Materia prima encontrada',
                    'data' => $rawMaterial,
                ], 200);
            } else {
                // Si no existe, retorna mensaje de error
                return response()->json([
                    'status' => false,
                    'message' => 'No se encuentra la materia prima',
                ], 404);
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
     * Muestra el formulario para editar una materia prima específica.
     * (No implementado)
     *
     * @param  string  $id  Identificador de la materia prima.
     * @return void
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Actualiza la información de una materia prima específica en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request  Datos de la solicitud HTTP.
     * @param  string  $id  Identificador de la materia prima.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el resultado de la operación.
     */
    public function update(Request $request, string $id)
    {
        // Validación de los datos recibidos
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'code' => 'sometimes|required|string|max:100|unique:raw_materials,code,'.$id,
            'description' => 'nullable|string',
            'unit_of_measure' => 'sometimes|required|string|max:50',
            'stock' => 'sometimes|required|numeric|min:0',
            'min_stock' => 'nullable|numeric|min:0',
            'is_active' => 'sometimes|required|boolean',
            'created_by' => 'nullable|integer|exists:users,id',
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
            // Busca la materia prima por su ID
            $rawMaterial = RawMaterial::find($id);
            if ($rawMaterial) {
                // Actualiza la materia prima con los datos validados
                $rawMaterial->update($request->all());

                return response()->json([
                    'status' => true,
                    'message' => 'Materia prima actualizada exitosamente',
                    'data' => $rawMaterial,
                ], 200);
            } else {
                // Si no existe, retorna mensaje de error
                return response()->json([
                    'status' => false,
                    'message' => 'No se encuentra la materia prima',
                ], 404);
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
     * Elimina una materia prima específica de la base de datos.
     *
     * @param  string  $id  Identificador de la materia prima.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el resultado de la operación.
     */
    public function destroy(string $id)
    {
        try {
            // Busca la materia prima por su ID
            $rawMaterial = RawMaterial::find($id);
            if ($rawMaterial) {
                // Elimina la materia prima
                $rawMaterial->delete();

                return response()->json([
                    'status' => true,
                    'message' => 'Materia prima eliminada exitosamente',
                ], 200);
            } else {
                // Si no existe, retorna mensaje de error
                return response()->json([
                    'status' => false,
                    'message' => 'No se encuentra la materia prima',
                ], 404);
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
}
