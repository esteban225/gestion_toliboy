<?php

namespace App\Modules\Products\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Products\Application\UseCases\ProductUseCase;
use App\Modules\Products\Http\Requests\RegisterRequest;
use App\Modules\Products\Http\Requests\UpdateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

/**
 * @group Productos
 *
 * @description Endpoints para la gestión de productos.
 */
class ProductController extends Controller
{
    private ProductUseCase $useCase;

    public function __construct(ProductUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    /**
     * Listar productos
     *
     * Obtiene una lista de productos, con filtros opcionales.
     *
     * @queryParam category string Opcional. Filtra los productos por categoría. Example: "Harinas"
     * @queryParam is_active boolean Opcional. Filtra por productos activos (1) o inactivos (0). Example: 1
     *
     * @response 200 {
     *  "success": true,
     *  "data": [
     *      {
     *          "id": 1,
     *          "name": "Harina de Trigo",
     *          "code": "PROD-001",
     *          "category": "Harinas",
     *          "description": "Harina de trigo de alta calidad.",
     *          "specifications": {"gluten": "12%", "protein": "10g"},
     *          "unit_price": 15.50,
     *          "is_active": true,
     *          "created_by": 1
     *      }
     *  ]
     * }
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['category', 'is_active']);
        $products = $this->useCase->list($filters);

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    /**
     * Mostrar un producto
     *
     * Obtiene los detalles de un producto específico por su ID.
     *
     * @urlParam id integer required El ID del producto. Example: 1
     *
     * @response 200 {
     *  "success": true,
     *  "data": {
     *      "id": 1,
     *      "name": "Harina de Trigo",
     *      "code": "PROD-001",
     *      "category": "Harinas",
     *      "description": "Harina de trigo de alta calidad.",
     *      "specifications": {"gluten": "12%", "protein": "10g"},
     *      "unit_price": 15.50,
     *      "is_active": true,
     *      "created_by": 1
     *  }
     * }
     * @response 404 {
     *  "success": false,
     *  "message": "El producto con ID 1 no existe."
     * }
     */
    public function show(int $id): JsonResponse
    {
        $product = $this->useCase->find($id);

        if (! $product) {
            return response()->json([
                'success' => false,
                'message' => "El producto con ID {$id} no existe.",
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $product,
        ]);
    }

    /**
     * Crear un producto
     *
     * Registra un nuevo producto en el sistema.
     *
     * @bodyParam name string required Nombre del producto. Example: "Levadura Seca"
     * @bodyParam code string required Código único del producto. Example: "PROD-002"
     * @bodyParam category string required Categoría del producto. Example: "Ingredientes"
     * @bodyParam description string Descripción del producto. Example: "Levadura para panadería."
     * @bodyParam specifications object Especificaciones técnicas en formato JSON. Example: {"type": "instant", "origin": "local"}
     * @bodyParam unit_price number required Precio unitario. Example: 25.00
     * @bodyParam is_active boolean Estado del producto. Example: true
     * @bodyParam created_by integer ID del usuario que crea el producto. Example: 1
     *
     * @response 201 {
     *  "success": true,
     *  "data": { "id": 2, "..."},
     *  "message": "Producto creado correctamente."
     * }
     * @response 422 {
     *  "success": false,
     *  "message": "El código ya existe."
     * }
     */
    public function store(RegisterRequest $request): JsonResponse
    {
        try {
            $product = $this->useCase->create($request->validated());

            return response()->json([
                'success' => true,
                'data' => $product,
                'message' => 'Producto creado correctamente.',
            ], 201);
        } catch (InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Actualizar un producto
     *
     * Modifica los datos de un producto existente.
     *
     * @urlParam id integer required El ID del producto a actualizar. Example: 1
     *
     * @bodyParam name string Nombre del producto. Example: "Harina de Trigo Integral"
     * @bodyParam unit_price number Precio unitario. Example: 18.75
     * @bodyParam is_active boolean Estado del producto. Example: false
     *
     * @response 200 {
     *  "success": true,
     *  "data": { "id": 1, "name": "Harina de Trigo Integral", "..."},
     *  "message": "Producto actualizado correctamente."
     * }
     * @response 422 {
     *  "success": false,
     *  "message": "El producto con ID 99 no existe."
     * }
     */
    public function update(UpdateRequest $request, int $id): JsonResponse
    {
        try {
            $product = $this->useCase->update($request->validated(), $id);

            return response()->json([
                'success' => true,
                'data' => $product,
                'message' => 'Producto actualizado correctamente.',
            ]);
        } catch (InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Eliminar un producto
     *
     * Elimina un producto del sistema por su ID.
     *
     * @urlParam id integer required El ID del producto a eliminar. Example: 2
     *
     * @response 200 {
     *  "success": true,
     *  "message": "Producto con ID 2 eliminado correctamente."
     * }
     * @response 422 {
     *  "success": false,
     *  "message": "El producto con ID 99 no existe."
     * }
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->useCase->delete($id);

            return response()->json([
                'success' => true,
                'message' => "Producto con ID {$id} eliminado correctamente.",
            ]);
        } catch (InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
