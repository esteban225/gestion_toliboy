<?php

namespace App\Modules\DataUser\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\DataUser\Application\UseCases\ManageDataUserUseCase;
use App\Modules\DataUser\Http\Requests\RegisterRequest;
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
        $filters = $request->except(['page', 'per_page']);
        $perPage = $request->input('per_page', 15);

        $paginator = $this->useCase->paginate($filters, $perPage);

        return response()->json([
            'status' => true,
            'message' => 'Datos de usuario paginados',
            'data' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }

    /**
     * Consultar datos de usuario por su identificador único.
     *
     * @param  string  $id  Identificador único de los datos de usuario.
     * @return JsonResponse Respuesta JSON con los datos del usuario.
     */
    public function show(string $id): JsonResponse
    {
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
        ]);
    }

    /**
     * Crear nuevos datos de usuario.
     *
     * @param  RegisterRequest  $request  Objeto HTTP con los datos validados.
     * @return JsonResponse Respuesta JSON con los datos creados.
     */
    public function store(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();
        $createdDataUser = $this->useCase->create($data);

        if (! $createdDataUser) {
            return response()->json([
                'status' => false,
                'message' => 'Error al crear los datos de usuario',
                'data' => null,
            ], 500);
        }

        return response()->json([
            'status' => true,
            'message' => 'Datos de usuario creados exitosamente',
            'data' => $createdDataUser,
        ], 201);
    }

    /**
     * Actualizar datos de usuario existentes.
     *
     * @param  RegisterRequest  $request  Objeto HTTP con los datos validados (debe incluir el id).
     * @return JsonResponse Respuesta JSON confirmando la actualización.
     */
    public function update(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();
        $updated = $this->useCase->update($data);

        if (! $updated) {
            return response()->json([
                'status' => false,
                'message' => 'Error al actualizar los datos de usuario',
            ], 500);
        }

        return response()->json([
            'status' => true,
            'message' => 'Datos de usuario actualizados exitosamente',
        ]);
    }

    /**
     * Eliminar datos de usuario por su identificador único.
     *
     * @param  string  $id  Identificador único de los datos de usuario.
     * @return JsonResponse Respuesta JSON confirmando la eliminación.
     */
    public function destroy(string $id): JsonResponse
    {
        $deleted = $this->useCase->delete($id);

        if (! $deleted) {
            return response()->json([
                'status' => false,
                'message' => 'Error al eliminar los datos de usuario',
            ], 500);
        }

        return response()->json([
            'status' => true,
            'message' => 'Datos de usuario eliminados exitosamente',
        ]);
    }
}
