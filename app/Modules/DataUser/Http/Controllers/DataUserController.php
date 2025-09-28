<?php

namespace App\Modules\DataUser\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\DataUser\Application\UseCases\ManageDataUserUseCase;
use App\Modules\DataUser\Http\Requests\DataUserRegisterRequest as RegisterRequest;
use App\Modules\DataUser\Http\Requests\DataUserUpDateRequest as UpDateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class DataUserController
 *
 * Controlador REST responsable de gestionar las operaciones CRUD de datos adicionales de usuarios.
 * Toda la lógica de negocio se delega al caso de uso {@see ManageDataUserUseCase}.
 *
 * Principios SOLID aplicados:
 * - SRP (Single Responsibility Principle): El controlador únicamente coordina peticiones y respuestas HTTP.
 * - DIP (Dependency Inversion Principle): Depende de la abstracción del caso de uso, no de implementaciones concretas.
 */
class DataUserController extends Controller
{
    /**
     * @var ManageDataUserUseCase Caso de uso encargado de la gestión de datos de usuario
     */
    private ManageDataUserUseCase $useCase;

    /**
     * Constructor.
     *
     * @param  ManageDataUserUseCase  $useCase  Caso de uso para gestión de datos de usuario
     */
    public function __construct(ManageDataUserUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    /**
     * Listar todos los datos de usuario con posibilidad de filtros.
     *
     * @param  Request  $request  Objeto HTTP con filtros opcionales.
     * @return JsonResponse Respuesta JSON con el listado de datos de usuario.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['user_id', 'type', 'created_at']); // Ejemplo de filtros
            $perPage = (int) $request->get('per_page', 15); // Paginación, por defecto 15 por página
            $dataUsers = $this->useCase->paginate($filters, $perPage);

            if ($dataUsers->isEmpty()) {
                return response()->json([
                    'status' => true,
                    'message' => 'No se encontraron datos de usuario',
                    'data' => [],
                    'meta' => [
                        'total' => 0,
                        'per_page' => $perPage,
                        'current_page' => 1,
                        'last_page' => 0,
                    ],
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Listado de datos de usuario',
                'data' => $dataUsers->items(),
                'meta' => [
                    'total' => $dataUsers->total(),
                    'per_page' => $dataUsers->perPage(),
                    'current_page' => $dataUsers->currentPage(),
                    'last_page' => $dataUsers->lastPage(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener el listado de datos de usuario',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Consultar datos de usuario por su identificador único.
     *
     * @param  string  $id  Identificador único de los datos de usuario.
     * @return JsonResponse Respuesta JSON con los datos del usuario.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $dataUser = $this->useCase->get($id);

            if (! $dataUser) {
                return response()->json([
                    'status' => false,
                    'message' => 'Datos de usuario no encontrados',
                    'data' => null,
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Datos de usuario encontrados',
                'data' => $dataUser,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener los datos de usuario',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Crear nuevos datos de usuario.
     *
     * @param  RegisterRequest  $request  Objeto HTTP con los datos validados.
     * @return JsonResponse Respuesta JSON con los datos creados.
     */
    public function store(RegisterRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $createdDataUser = $this->useCase->create($data);

            return response()->json([
                'status' => true,
                'message' => 'Datos de usuario creados exitosamente',
                'data' => $createdDataUser,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al crear los datos de usuario',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Actualizar datos de usuario existentes.
     *
     * @param  UpDateRequest  $request  Objeto HTTP con los datos validados (debe incluir el id).
     * @return JsonResponse Respuesta JSON confirmando la actualización.
     */
    public function update(UpDateRequest $request, string $id): JsonResponse
    {
        try {
            $data = $request->validated();

            $updatedDataUser = $this->useCase->update($id, $data);
            if (! $updatedDataUser) {
                return response()->json([
                    'status' => false,
                    'message' => 'Datos de usuario no encontrados para actualizar',
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Datos de usuario actualizados exitosamente',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al actualizar los datos de usuario',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Eliminar datos de usuario por su identificador único.
     *
     * @param  string  $id  Identificador único de los datos de usuario.
     * @return JsonResponse Respuesta JSON confirmando la eliminación.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $deleted = $this->useCase->delete($id);
            if (! $deleted) {
                return response()->json([
                    'status' => false,
                    'message' => 'Datos de usuario no encontrados para eliminar',
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Datos de usuario eliminados exitosamente',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al eliminar los datos de usuario',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
