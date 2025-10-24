<?php

namespace App\Modules\Users\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Users\Aplication\UseCases\ManageUserUseCase;
use App\Modules\Users\Domain\Entities\UserEntity;
use App\Modules\Users\Http\Requests\UserFilterRequest;
use App\Modules\Users\Http\Requests\UserRegisterRequest as RegisterRequest;
use App\Modules\Users\Http\Requests\UserUpDateRequest as UpDateRequest;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Class UsersController
 *
 * Controlador REST responsable de las operaciones CRUD sobre usuarios.
 *
 * Breve descripción:
 * Este controlador actúa como una capa de presentación que orquesta las
 * peticiones HTTP relacionadas con usuarios (listar, ver, crear, actualizar,
 * eliminar). Toda la lógica de negocio se delega al caso de uso
 * {@see ManageUserUseCase} para mantener la separación de responsabilidades.
 *
 * Principios aplicados:
 * - SRP: Solo coordina la entrada/salida HTTP.
 * - DIP: Depende de la abstracción del caso de uso.
 */
#[Group(name: 'Modulo de Usuarios: usuarios', weight: 2)]
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
     * Listar todos los usuarios con filtros.
     *
     * Este método obtiene una lista paginada de usuarios aplicando filtros
     * provistos en la request. En la respuesta, la contraseña siempre se
     * enmascara por seguridad.
     *
     * Filtros soportados: name, is_active, per_page, page.
     *
     * Esta acción responde bajo estos roles:
     *
     * - DEV = Desarrollador
     * - GG = Gerente General
     * - INGPL = Ingeniero de Planta
     * - INGPR = Ingeniero de Producción
     *
     * @param  UserFilterRequest  $request  Request validada con filtros
     * @return JsonResponse Respuesta HTTP con datos paginados y meta
     */
    public function index(UserFilterRequest $request): JsonResponse
    {
        try {
            $filters = $request->except(['per_page', 'page']); // Ejemplo de filtros
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
     * Consultar usuario por ID.
     *
     * Recupera un usuario por su identificador y aplica una máscara a la
     * contraseña antes de retornar la entidad.
     *
     * @param  int  $id  Identificador del usuario
     * @return JsonResponse Respuesta HTTP con la entidad del usuario o 404
     */
    public function show(int $id): JsonResponse
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
     * Recibe datos validados por {@see RegisterRequest}, construye una
     * {@see UserEntity} y delega la creación al caso de uso.
     *
     * Esta acción responde bajo estos roles:
     *
     * - DEV = Desarrollador
     * - GG = Gerente General
     * - INGPL = Ingeniero de Planta
     * - INGPR = Ingeniero de Producción
     *
     * @param  RegisterRequest  $request  Request con datos validados
     * @return JsonResponse 201 en creación exitosa, 500 en error
     */
    public function store(RegisterRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $user = UserEntity::fromArray($data);
            $userCreated = $this->useCase->create($user);

            // Convertimos la entidad a array
            $userArray = $userCreated ? $userCreated->toArray() : [];

            // Si hay contraseña, la ocultamos
            if (isset($userArray['password'])) {
                $userArray['password'] = str_repeat('*', 8); // Máscara de 8 asteriscos
            }

            return response()->json([
                'status' => true,
                'message' => 'Usuario creado exitosamente',
                'data' => $userArray,
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
     * Valida la entrada con {@see UpDateRequest}, construye la entidad y
     * solicita al caso de uso la actualización. Retorna 404 si no existe.
     *
     * @param  UpDateRequest  $request  Request con datos validados
     * @param  int  $id  Identificador del usuario a actualizar
     * @return JsonResponse 200 en éxito, 404 si no existe, 500 en error
     */
    public function update(UpDateRequest $request, int $id): JsonResponse
    {
        try {
            $data = $request->validated();

            $updatedUser = $this->useCase->update($id, $data);

            if (! $updatedUser) {
                return response()->json([
                    'status' => false,
                    'message' => 'Usuario no encontrado para actualizar',
                ], 404);
            }
            return response()->json([
                'status' => true,
                'message' => 'Usuario actualizado exitosamente',
                'data' => $updatedUser->toArray(),
            ], 200);

        } catch (\Exception $e) {
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
     * Delegates the deletion to the use case and returns appropriate
     * HTTP codes depending on the outcome.
     *
     * Esta acción responde bajo estos roles:
     *
     * - DEV = Desarrollador
     * - GG = Gerente General
     * - INGPL = Ingeniero de Planta
     * - INGPR = Ingeniero de Producción
     *
     * @param  int  $id  Identificador del usuario a eliminar
     * @return JsonResponse 200 si se elimina, 404 si no existe
     */
    public function destroy(int $id): JsonResponse
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
