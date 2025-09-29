<?php

namespace App\Modules\Users\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Users\Aplication\UseCases\ManageUserUseCase;
use App\Modules\Users\Domain\Entities\UserEntity;
use App\Modules\Users\Http\Requests\UserRegisterRequest as RegisterRequest;
use App\Modules\Users\Http\Requests\UserUpDateRequest as UpDateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Class UsersController
 *
 * Controlador REST responsable de gestionar las operaciones CRUD de usuarios.
 * Toda la lógica de negocio se delega al caso de uso {@see ManageUserUseCase}.
 *
 * Principios SOLID aplicados:
 * - SRP (Single Responsibility Principle): El controlador únicamente coordina peticiones y respuestas HTTP.
 * - DIP (Dependency Inversion Principle): Depende de la abstracción del caso de uso, no de implementaciones concretas.
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
     * @param  ManageUserUseCase  $useCase  Caso de uso para gestión de usuarios
     */
    public function __construct(ManageUserUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    /**
     * Listar todos los usuarios con posibilidad de filtros.
     *
     * @param  Request  $request  Objeto HTTP con filtros opcionales.
     * @return JsonResponse Respuesta JSON con el listado de usuarios.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['user_id', 'type', 'created_at']); // Ejemplo de filtros
            $perPage = (int) $request->get('per_page', 15); // Paginación, por defecto 15 por página
            $dataUsers = $this->useCase->paginate($filters, $perPage);

            $users = collect($dataUsers->items())->map(function ($user) {
                if (isset($user['password'])) {
                    $user['password'] = str_repeat('*', 8); // Máscara fija de 8 asteriscos
                }

                return $user;
            })->toArray();

            if (empty($users)) {
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
                'data' => $users,
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
     * Consultar usuario por su identificador único.
     *
     * @param  string  $id  Identificador del usuario.
     * @return JsonResponse Respuesta JSON con el usuario o error si no se encuentra.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $dataUsers = $this->useCase->get($id);

            if (! $dataUsers) {
                return response()->json([
                    'status' => false,
                    'message' => 'Usuario no encontrado',
                ], 404);
            }
            $user = Collect($dataUsers ? $dataUsers->toArray() : [])->map(function ($value, $key) {
                if ($key === 'password') {
                    return str_repeat('*', 8); // Máscara fija de 8 asteriscos
                }

                return $value;
            })->toArray();
            $data = UserEntity::fromArray($user ? $user : []);

            return response()->json([
                'status' => true,
                'message' => 'Usuario encontrado',
                'data' => $data->toArray(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener el usuario',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Crear un nuevo usuario.
     *
     * @param  RegisterRequest  $request  Objeto con validaciones previas.
     * @return JsonResponse Respuesta JSON confirmando la creación o error.
     */
    public function store(RegisterRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $user = UserEntity::fromArray($data);
            $this->useCase->create($user);

            return response()->json([
                'status' => true,
                'message' => 'Usuario creado exitosamente',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al crear el usuario',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Actualizar un usuario existente.
     *
     * @param  UpDateRequest  $request  Objeto con validaciones previas.
     * @param  string  $id  Identificador del usuario a actualizar.
     * @return JsonResponse Respuesta JSON confirmando la actualización o error.
     */
    /**
     * Actualiza un usuario.
     *
     * @authenticated
     *
     * @group Usuarios
     *
     * @urlParam id integer required ID del usuario.
     *
     * @bodyParam name string Nombre.
     * @bodyParam email string Email.
     * @bodyParam password string Nueva contraseña (opcional).
     * @bodyParam role_id string Rol (opcional).
     * @bodyParam position string Cargo (opcional).
     * @bodyParam is_active boolean Estado (opcional).
     */
    public function update(UpDateRequest $request, string $id)
    {
        try {
            $data = $request->validated();
            Log::debug('UsersController.update.validated', $data);

            if ($data) {
                $data = $request->all();
                Log::debug('UsersController.update.all', $data);
            }
            $user = UserEntity::fromArray($data);
            $updatedDataUser = $this->useCase->update($id, $user);
            if (! $updatedDataUser) {
                return response()->json([
                    'status' => false,
                    'message' => 'Usuario no encontrado para actualizar',
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Usuario actualizado exitosamente',
            ], 200);
        } catch (\Exception $e) {
            Log::error('UserController: Error al actualizar usuario:', ['error' => $e]);

            return response()->json([
                'status' => false,
                'message' => 'Error al actualizar el usuario',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Eliminar un usuario por ID.
     *
     * @param  string  $id  Identificador del usuario a eliminar.
     * @return JsonResponse Respuesta JSON confirmando la eliminación o error.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $deleted = $this->useCase->delete($id);
            if (! $deleted) {
                return response()->json([
                    'status' => false,
                    'message' => 'Usuario no encontrado para eliminar',
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Usuario eliminado exitosamente',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al eliminar el usuario',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
