<?php

namespace App\Modules\Users\Http\Controllers;

use App\Modules\Users\Aplication\UseCases\ManageUserUseCase;
use App\Modules\Users\Http\Requests\RegisterRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Class UsersController
 *
 * Controlador REST responsable de gestionar las operaciones CRUD de usuarios.
 * Toda la lógica de negocio se delega al caso de uso {@see ManageUserUseCase}.
 *
 * Principios SOLID aplicados:
 * - SRP (Single Responsibility Principle): El controlador únicamente coordina peticiones y respuestas HTTP.
 * - DIP (Dependency Inversion Principle): Depende de la abstracción del caso de uso, no de implementaciones concretas.
 *
 * @package App\Modules\Users\Http\Controllers
 */
class UsersController extends Controller
{
    /**
     * @var ManageUserUseCase Caso de uso encargado de la gestión de usuarios
     */
    private ManageUserUseCase $useCase;

    /**
     * Constructor.
     *
     * @param ManageUserUseCase $useCase Caso de uso para gestión de usuarios
     */
    public function __construct(ManageUserUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    /**
     * Listar todos los usuarios con posibilidad de filtros.
     *
     * @param Request $request Objeto HTTP con filtros opcionales.
     * @return JsonResponse Respuesta JSON con el listado de usuarios.
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->all();
        $users = $this->useCase->list($filters);

        return response()->json([
            'status' => true,
            'message' => 'Usuarios encontrados',
            'data' => $users
        ]);
    }

    /**
     * Consultar usuario por su identificador único.
     *
     * @param string $id Identificador del usuario.
     * @return JsonResponse Respuesta JSON con el usuario o error si no se encuentra.
     */
    public function show(string $id): JsonResponse
    {
        $users = $this->useCase->get($id);

        if (!$users) {
            return response()->json([
                'status' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Usuario encontrado',
            'data' => $users
        ]);
    }

    /**
     * Crear un nuevo usuario.
     *
     * @param RegisterRequest $request Objeto con validaciones previas.
     * @return JsonResponse Respuesta JSON confirmando la creación o error.
     */
    public function store(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();
        $users = $this->useCase->create($data);

        if (!$users) {
            return response()->json([
                'status' => false,
                'message' => 'Error al crear el usuario'
            ], 500);
        }

        return response()->json([
            'status' => true,
            'message' => 'Usuario creado',
        ], 201);
    }

    /**
     * Actualizar un usuario existente.
     *
     * @param RegisterRequest $request Objeto con validaciones previas.
     * @param string $id Identificador del usuario a actualizar.
     * @return JsonResponse Respuesta JSON confirmando la actualización o error.
     */
    public function update(RegisterRequest $request, string $id): JsonResponse
    {
        $data = $request->validated();
        $updated = $this->useCase->update($id, $data);

        if (!$updated) {
            return response()->json([
                'message' => 'Usuario no encontrado o no actualizado'
            ], 404);
        }

        return response()->json([
            'message' => 'ok'
        ], 200);
    }

    /**
     * Eliminar un usuario por ID.
     *
     * @param string $id Identificador del usuario a eliminar.
     * @return JsonResponse Respuesta JSON confirmando la eliminación o error.
     */
    public function destroy(string $id): JsonResponse
    {
        $deleted = $this->useCase->delete($id);

        if (!$deleted) {
            return response()->json([
                'message' => 'Usuario no encontrado o no eliminado'
            ], 404);
        }

        return response()->json([
            'message' => 'ok'
        ], 200);
    }
}
