<?php

namespace App\Modules\DataUser\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\DataUser\Application\UseCases\ManageDataUserUseCase;
use App\Modules\DataUser\Http\Requests\DataUserFilterRequest;
use App\Modules\DataUser\Http\Requests\DataUserRegisterRequest as RegisterRequest;
use App\Modules\DataUser\Http\Requests\DataUserUpDateRequest as UpDateRequest;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class DataUserController
 *
 * Controlador REST responsable de gestionar las operaciones CRUD de datos
 * adicionales asociados a usuarios.
 *
 * Breve descripción:
 * Este controlador expone endpoints para listar, ver, crear, actualizar y
 * eliminar datos adicionales de usuarios. Toda la lógica de dominio se
 * delega al caso de uso {@see ManageDataUserUseCase} para mantener la
 * separación de responsabilidades.
 *
 * Principios SOLID aplicados:
 * - SRP: Coordina exclusivamente la interacción HTTP.
 * - DIP: Depende de la abstracción del caso de uso.
 */
#[Group(name: 'Modulo de Usuarios: datos de usuario', weight: 3)]
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
     * Listar datos con filtros.
     *
     * Obtiene un listado paginado de datos adicionales de usuario aplicando
     * filtros opcionales incluidos en la request. Retorna meta información de
     * paginación en la respuesta.
     *
     * Esta acción responde bajo estos roles:
     *
     * - DEV = Desarrollador
     * - GG = Gerente General
     * - INGPL = Ingeniero de Planta
     * - INGPR = Ingeniero de Producción
     *
     * @param  DataUserFilterRequest  $request  Request validada con filtros
     * @return JsonResponse Respuesta HTTP con datos y metadatos de paginación
     */
    public function index(DataUserFilterRequest $request): JsonResponse
    {
        try {
            $filters = $request->except(['page', 'per_page']);
            $perpage = $request->input('per_page', 15);
            $dataUsers = $this->useCase->paginate($filters, $perpage);

            if ($dataUsers->isEmpty()) {
                return response()->json([
                    'status' => true,
                    'message' => 'No se encontraron datos de usuario',
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
     * Consultar por su identificador único.
     *
     * Recupera los datos adicionales por su ID. Si no existe, retorna 404.
     *
     * @param  int  $id  Identificador único de los datos de usuario
     * @return JsonResponse Respuesta HTTP con la entidad de datos o 404
     */
    public function show(int $id): JsonResponse
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
     * Crear datos adicionales de usuario.
     *
     * Recibe una request validada por {@see RegisterRequest} y delega la
     * creación al caso de uso. Retorna 201 con la entidad creada.
     *
     * @param  RegisterRequest  $request  Request validada con los datos
     * @return JsonResponse 201 con la entidad creada o 500 en error
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
     * Actualizar datos adicionales de usuario.
     *
     * Valida la request y solicita al caso de uso la actualización del registro
     * identificado por $id. Retorna 404 si no existe.
     *
     * @param  UpDateRequest  $request  Request validada con los datos
     * @param  int  $id  Identificador único del registro a actualizar
     * @return JsonResponse 200 en éxito, 404 si no existe, 500 en error
     */
    public function update(UpDateRequest $request, int $id): JsonResponse
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
     * Eliminar datos.
     *
     * Esta acción responde bajo estos roles:
     *
     * - DEV = Desarrollador
     * - GG = Gerente General
     * - INGPL = Ingeniero de Planta
     * - INGPR = Ingeniero de Producción
     *
     * @param  int  $id  Identificador único de los datos de usuario.
     * @return JsonResponse Respuesta JSON confirmando la eliminación.
     */
    public function destroy(int $id): JsonResponse
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
