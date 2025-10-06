<?php

namespace App\Modules\Products\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Products\Application\UseCases\ProductUseCase;
use App\Modules\Products\Domain\Entities\ProductEntity;
use App\Modules\Products\Http\Requests\ProductFilterRequest;
use App\Modules\Products\Http\Requests\ProductRegisterRequest;
use App\Modules\Products\Http\Requests\ProductUpdateRequest;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use InvalidArgumentException;

/**
 * @group Productos
 *
 * @description Endpoints para la gestión de productos.
 */
#[Group(name: 'Modulo de Inventario: Productos', weight: 6)]
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
     * Obtiene una lista de productos, con filtros opcionales y paginación.
     * Maneja la validación de los parámetros de filtrado para listar productos.
     * Esto es opcional si se quiere filtrar productos por ciertos criterios.
     * Si no se proporcionan parámetros, se listan todos los productos con paginación.
     *
     * Filtros soportados: name, category, is_active, per_page, page.
     *
     * Roles permitidos:
     * - DEV = Desarrollador
     * - GG = Gerente General
     * - INGPL = Ingeniero de Planta
     * - INGPR = Ingeniero de Producción
     */
    public function index(ProductFilterRequest $request): JsonResponse
    {
        try {
            $filters = $request->except(['page', 'per_page']);
            $perPage = $request->input('per_page', 15);

            $paginator = $this->useCase->list($filters, $perPage);

            if (! $paginator) {
                return response()->json(['message' => 'No se encontraron productos'], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $paginator->items(),
                'meta' => [
                    'current_page' => $paginator->currentPage(),
                    'last_page' => $paginator->lastPage(),
                    'per_page' => $paginator->perPage(),
                    'total' => $paginator->total(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error retrieving products', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Mostrar un producto
     *
     * Obtiene los detalles de un producto específico por su ID.
     *
     * Roles permitidos:
     * - DEV = Desarrollador
     * - GG = Gerente General
     * - INGPL = Ingeniero de Planta
     * - INGPR = Ingeniero de Producción
     *
     * @param  int  $id  ID del producto a consultar
     */
    public function show(int $id): JsonResponse
    {
        try {
            $product = $this->useCase->find($id);

            if (! $product) {
                return response()->json([
                    'success' => false,
                    'message' => "El producto con ID {$id} no existe.",
                ], 404);
            }

            $product = $product->toArray();

            return response()->json([
                'success' => true,
                'message' => 'Producto recuperado correctamente.',
                'data' => $product,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error retrieving product', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Crear un producto
     *
     * Registra un nuevo producto en el sistema.
     *
     * Roles permitidos:
     * - DEV = Desarrollador
     * - GG = Gerente General
     * - INGPL = Ingeniero de Planta
     * - INGPR = Ingeniero de Producción
     */
    public function store(ProductRegisterRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $product = ProductEntity::fromArray($data);
            $request = $this->useCase->create($product);

            if (! $request) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al crear el producto.',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Producto creado correctamente.',
            ], 201);
        } catch (InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el producto.',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Actualizar un producto
     *
     * Modifica los datos de un producto existente.
     *
     * Roles permitidos:
     * - DEV = Desarrollador
     * - GG = Gerente General
     * - INGPL = Ingeniero de Planta
     * - INGPR = Ingeniero de Producción
     *
     * @param  int  $id  ID del producto a actualizar
     */
    public function update(ProductUpdateRequest $request, int $id): JsonResponse
    {
        try {
            $data = $request->validated();
            $product = ProductEntity::fromArray($data);
            $response = $this->useCase->update($product, $id);
            if (! $response) {
                return response()->json([
                    'success' => false,
                    'message' => "El producto con ID {$id} no existe o no se pudo actualizar.",
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Producto actualizado correctamente.',
            ], 200);
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
     * Roles permitidos:
     * - DEV = Desarrollador
     * - GG = Gerente General
     * - INGPL = Ingeniero de Planta
     * - INGPR = Ingeniero de Producción
     *
     * @param  int  $id  ID del producto a eliminar
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $response = $this->useCase->delete($id);

            if (! $response) {
                return response()->json([
                    'success' => false,
                    'message' => "El producto con ID {$id} no existe o no se pudo eliminar.",
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => "Producto con ID {$id} eliminado correctamente.",
            ], 200);
        } catch (InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
