<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Controlador para la gestión de productos.
 * Permite listar, crear, mostrar, actualizar y eliminar productos.
 */
class ProductsController extends Controller
{
    /**
     * Muestra una lista de todos los productos.
     *
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con todos los productos o mensaje de error.
     */
    public function index()
    {
        try {
            // Obtiene todos los productos de la base de datos
            $products = Product::all();

            if ($products->isEmpty()) {
                // Si no hay productos registrados
                return response()->json(['message' => 'No se encuentran productos'], 404);
            } else {
                // Si existen productos, retorna los datos
                return response()->json([
                    'status' => true,
                    'message' => 'Productos encontrados',
                    'data' => $products
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
     * Muestra el formulario para crear un nuevo producto.
     * (No implementado, normalmente usado en aplicaciones web con vistas)
     *
     * @return void
     */
    public function create() {}

    /**
     * Almacena un nuevo producto en la base de datos.
     *
     * @param \Illuminate\Http\Request $request Datos de la solicitud HTTP.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el resultado de la operación.
     */
    public function store(Request $request)
    {
        // Validación de los datos recibidos
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:100|unique:products,code',
            'category' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'specifications' => 'nullable|array',
            'unit_price' => 'nullable|numeric|min:0',
            'created_by' => 'nullable|integer|exists:users,id',
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
            // Crea el producto con los datos validados
            $product = Product::create($request->all());

            if ($product) {
                // Si se creó correctamente
                return response()->json([
                    'status' => true,
                    'message' => 'Producto creado exitosamente',
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
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Muestra la información de un producto específico.
     *
     * @param string $id Identificador del producto.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con los datos o mensaje de error.
     */
    public function show(string $id)
    {
        try {
            // Busca el producto por su ID
            $product = Product::find($id);

            if ($product) {
                // Si existe, retorna los datos
                return response()->json([
                    'status' => true,
                    'message' => 'Producto encontrado',
                    'data' => $product
                ], 200);
            } else {
                // Si no existe, retorna mensaje de error
                return response()->json([
                    'status' => false,
                    'message' => 'Producto no encontrado'
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
     * Muestra el formulario para editar un producto específico.
     * (No implementado)
     *
     * @param string $id Identificador del producto.
     * @return void
     */
    public function edit(string $id) {}

    /**
     * Actualiza la información de un producto específico en la base de datos.
     *
     * @param \Illuminate\Http\Request $request Datos de la solicitud HTTP.
     * @param string $id Identificador del producto.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el resultado de la operación.
     */
    public function update(Request $request, string $id)
    {
        // Validación de los datos recibidos
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'code' => 'sometimes|required|string|max:100|unique:products,code,' . $id,
            'category' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'specifications' => 'nullable|array',
            'unit_price' => 'nullable|numeric|min:0',
            'is_active' => 'sometimes|required|boolean',
            'created_by' => 'nullable|integer|exists:users,id',
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
            // Busca el producto por su ID
            $product = Product::find($id);

            if (!$product) {
                // Si no existe, retorna mensaje de error
                return response()->json([
                    'status' => false,
                    'message' => 'Producto no encontrado'
                ], 404);
            }

            // Actualiza el producto con los datos validados
            $product->update($request->all());

            // Retorna respuesta exitosa con los datos actualizados
            return response()->json([
                'status' => true,
                'message' => 'Producto actualizado exitosamente',
                'data' => $product
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
     * Elimina un producto específico de la base de datos.
     *
     * @param string $id Identificador del producto.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el resultado de la operación.
     */
    public function destroy(string $id)
    {
        try {
            // Busca el producto por su ID
            $product = Product::find($id);

            if (!$product) {
                // Si no existe, retorna mensaje de error
                return response()->json([
                    'status' => false,
                    'message' => 'Producto no encontrado'
                ], 404);
            }

            // Elimina el producto
            $product->delete();

            // Retorna respuesta exitosa
            return response()->json([
                'status' => true,
                'message' => 'Producto eliminado exitosamente'
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
