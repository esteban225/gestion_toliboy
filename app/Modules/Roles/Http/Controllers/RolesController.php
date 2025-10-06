<?php

namespace App\Modules\Roles\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Roles\Application\UseCases\ManageRoleUseCase;
use App\Modules\Roles\Http\Requests\RegisterRequest;
use Dedoc\Scramble\Attributes\Group;
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
#[Group(name: 'Modulo de Usuarios: Roles ', weight: 1)]
class RolesController extends Controller
{
    public function __construct(private ManageRoleUseCase $useCase) {}

    /**
     * Obtener un rol por ID
     *
     * Recupera la información de un rol específico según su identificador.
     *
     * Los roles que pueden acceder a esta acción son:
     *
     * - DEV = Desarrollador
     * - GG = Gerente General
     * - INGPL = Ingeniero de Planta
     * - INGPR = Ingeniero de Producción
     *
     * @param int  id int required El identificador único del rol. Ejemplo: 1
     *
     * PHPDoc adicional: types for IDEs and static analysis.
     * @param  int  $id  Identificador único del rol
     * @return JsonResponse Respuesta JSON con el rol o error 404
     */
    public function getById(int $id): JsonResponse
    {
        try {
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
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al obtener el rol', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Listar roles
     *
     * Devuelve una lista de roles registrados en el sistema.
     * Se puede aplicar filtrado opcional por nombre o descripción.
     *
     * Los roles que pueden acceder a esta acción son:
     *
     * - DEV = Desarrollador
     * - GG = Gerente General
     * - INGPL = Ingeniero de Planta
     * - INGPR = Ingeniero de Producción
     *
     * @query name int Filtrar por nombre del rol. Ejemplo: Admin
     *
     * @param description int Filtrar por descripción del rol. Ejemplo: "Rol con todos los permisos"
     *
     * PHPDoc adicional: types for IDEs and static analysis.
     * @param  Request  $request  Objeto Request con filtros opcionales (name, description)
     * @return JsonResponse Respuesta JSON con la lista de roles
     */
    public function list(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['name', 'description']);
            $roles = $this->useCase->list($filters);

            if (! $roles) {
                return response()->json(['message' => 'No se encontraron roles'], 404);
            }

            return response()->json(
                [
                    'status' => true,
                    'message' => 'Roles encontrados',
                    'data' => $roles,
                ],
                200
            );
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al listar los roles', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Crear un nuevo rol
     *
     * Registra un rol en el sistema.
     *
     * Los roles que pueden acceder a esta acción son:
     *
     * - DEV = Desarrollador
     * - GG = Gerente General
     * - INGPL = Ingeniero de Planta
     * - INGPR = Ingeniero de Producción
     *
     * PHPDoc adicional: types for IDEs and static analysis.
     *
     * @param  RegisterRequest  $request  Request con los campos necesarios para crear el rol (name, description)
     * @return JsonResponse Respuesta JSON con el id del rol creado (201) o error
     */
    public function create(RegisterRequest $request): JsonResponse
    {
        try {
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
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al crear el rol', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Actualizar un rol existente
     *
     * Permite modificar los datos de un rol.
     *
     * Los roles que pueden acceder a esta acción son:
     *
     * - DEV = Desarrollador
     * - GG = Gerente General
     * - INGPL = Ingeniero de Planta
     * - INGPR = Ingeniero de Producción
     *
     * PHPDoc adicional: types for IDEs and static analysis.
     *
     * @param  int  $id  Identificador del rol a actualizar
     * @param  RegisterRequest  $request  Request con los campos a actualizar (name, description)
     * @return JsonResponse Respuesta HTTP indicando el resultado de la actualización
     */
    public function update(int $id, RegisterRequest $request): JsonResponse
    {
        try {
            $data = $request->only(['name', 'description']);
            $updated = $this->useCase->update($id, $data);
            if (! $updated) {
                return response()->json(['message' => 'Rol no encontrado o no actualizado'], 404);
            }

            return response()->json(
                ['message' => 'ok'],
                200
            );
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al actualizar el rol', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Eliminar un rol
     *
     * Elimina un rol del sistema.
     *
     * Los roles que pueden acceder a esta acción son:
     *
     * - DEV = Desarrollador
     * - GG = Gerente General
     * - INGPL = Ingeniero de Planta
     * - INGPR = Ingeniero de Producción
     *
     * PHPDoc adicional: types for IDEs and static analysis.
     *
     * @param  int  $id  Identificador del rol a eliminar
     * @return JsonResponse Respuesta HTTP indicando si se eliminó correctamente
     */
    public function delete(int $id): JsonResponse
    {

        try {
            $deleted = $this->useCase->delete($id);
            if (! $deleted) {
                return response()->json(['message' => 'Rol no encontrado o no eliminado'], 404);
            }

            return response()->json(['message' => 'ok'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al eliminar el rol', 'error' => $e->getMessage()], 500);
        }
    }
}
