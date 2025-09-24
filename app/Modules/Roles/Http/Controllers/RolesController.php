<?php

namespace App\Modules\Roles\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Roles\Application\UseCases\ManageRoleUseCase;
use App\Modules\Roles\Http\Requests\RegisterRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Roles
 *
 * API para la gestión de roles en el sistema.
 *
 * Este módulo permite crear, consultar, actualizar y eliminar roles
 * que definen permisos y accesos dentro de la aplicación.
 */
class RolesController extends Controller
{
    public function __construct(private ManageRoleUseCase $useCase) {}

    /**
     * Obtener un rol por ID
     *
     * Recupera la información de un rol específico según su identificador.
     *
     * @urlParam id string required El identificador único del rol. Ejemplo: 1
     *
     * @response 200 {
     *   "id": "1",
     *   "name": "Admin",
     *   "description": "Administrador"
     * }
     * @response 404 {
     *   "message": "Rol no encontrado"
     * }
     */
    public function getById(string $id): JsonResponse
    {
        $role = $this->useCase->getById($id);
        if (! $role) {
            return response()->json(['message' => 'Rol no encontrado'], 404);
        }

        return response()->json(
            [
                'status' => true,
                'message' => 'Rol encontrado',
                'data' => $role,
            ],
            200
        );
    }

    /**
     * Listar roles
     *
     * Devuelve una lista de roles registrados en el sistema.
     * Se puede aplicar filtrado opcional por nombre o descripción.
     *
     * @queryParam name string Filtrar por nombre del rol. Ejemplo: Admin
     * @queryParam description string Filtrar por descripción del rol. Ejemplo: "Rol con todos los permisos"
     *
     * @response 200 [
     *   {
     *     "id": "1",
     *     "name": "Admin",
     *     "description": "Administrador"
     *   }
     * ]
     */
    public function list(Request $request): JsonResponse
    {
        $filters = $request->only(['name', 'description']);
        $roles = $this->useCase->list($filters);

        return response()->json(
            [
                'status' => true,
                'message' => 'Roles encontrados',
                'data' => $roles,
            ],
            200
        );
    }

    /**
     * Crear un nuevo rol
     *
     * Registra un rol en el sistema.
     *
     * @bodyParam name string required Nombre del rol. Ejemplo: Editor
     * @bodyParam description string Descripción del rol. Ejemplo: "Puede editar artículos"
     *
     * @response 201 {
     *   "id": "2"
     * }
     */
    public function create(RegisterRequest $request): JsonResponse
    {
        $data = $request->only(['name', 'description']);
        $role = $this->useCase->create($data);

        return response()->json(
            [
                'status' => true,
                'message' => 'Rol creado exitosamente',
                'data' => ['id' => $role->id],
            ],
            201
        );
    }

    /**
     * Actualizar un rol existente
     *
     * Permite modificar los datos de un rol.
     *
     * @urlParam id string required El identificador único del rol. Ejemplo: 2
     *
     * @bodyParam name string Nombre del rol. Ejemplo: Editor
     * @bodyParam description string Descripción del rol. Ejemplo: "Puede editar artículos"
     *
     * @response 200 {
     *   "message": "ok"
     * }
     * @response 404 {
     *   "message": "Rol no encontrado o no actualizado"
     * }
     */
    public function update(string $id, RegisterRequest $request): JsonResponse
    {
        $data = $request->only(['name', 'description']);
        $updated = $this->useCase->update($id, $data);
        if (! $updated) {
            return response()->json(['message' => 'Rol no encontrado o no actualizado'], 404);
        }

        return response()->json(
            ['message' => 'ok'],
            200
        );
    }

    /**
     * Eliminar un rol
     *
     * Elimina un rol del sistema.
     *
     * @urlParam id string required El identificador único del rol. Ejemplo: 2
     *
     * @response 200 {
     *   "message": "ok"
     * }
     * @response 404 {
     *   "message": "Rol no encontrado o no eliminado"
     * }
     */
    public function delete(string $id): JsonResponse
    {
        $deleted = $this->useCase->delete($id);
        if (! $deleted) {
            return response()->json(['message' => 'Rol no encontrado o no eliminado'], 404);
        }

        return response()->json(['message' => 'ok'], 200);
    }
}
