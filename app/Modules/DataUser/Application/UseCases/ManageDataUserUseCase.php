<?php

namespace App\Modules\DataUser\Application\UseCases;

use App\Modules\DataUser\Domain\Services\DataUserService;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Caso de uso para la gestión de datos adicionales de usuarios.
 *
 * Orquesta las operaciones de listado, consulta, creación, actualización y eliminación de datos de usuario,
 * delegando la lógica de negocio al DataUserService.
 *
 * Principios SOLID aplicados:
 * - SRP: Cada método tiene una única responsabilidad (listar, obtener, crear, actualizar, eliminar).
 * - OCP: Puede extenderse con nuevos métodos sin modificar los existentes.
 * - LSP: Puede ser sustituido por otra implementación de caso de uso sin romper el sistema.
 * - ISP: Expone solo los métodos necesarios para la gestión de datos de usuario.
 * - DIP: Depende de la abstracción DataUserService, no de una implementación concreta.
 */
class ManageDataUserUseCase
{
    /**
     * @var DataUserService Servicio de datos de usuario
     */
    public function __construct(private DataUserService $dataUserService) {}

    /**
     * Listar datos de usuario con filtros.
     *
     * @param  array  $filters  Filtros de búsqueda (ej: ['status' => 'active'])
     */
    public function list(array $filters): array
    {
        // Convierte cada entidad a array usando el método toArray()
        return $this->dataUserService->listDataUsers($filters);
    }

    public function paginate(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->dataUserService->paginateDataUsers($filters, $perPage);

    }

    /**
     * Obtener datos de usuario por ID.
     *
     * @param  int  $id  Identificador único de los datos de usuario
     * @return mixed
     */
    public function get(int $id)
    {
        $entity = $this->dataUserService->getDataUser($id);

        return $entity && method_exists($entity, 'toArray') ? $entity->toArray() : $entity;
    }

    /**
     * Crear datos de usuario.
     *
     * @param  array  $data  Datos del usuario (campos específicos del módulo DataUser)
     * @return mixed
     */
    public function create(array $data)
    {
        $entity = $this->dataUserService->createDataUser($data);

        return $entity && method_exists($entity, 'toArray') ? $entity->toArray() : $entity;
    }

    /**
     * Actualizar datos de usuario.
     *
     * @param  int  $id  Identificador único de los datos de usuario
     * @param  array  $data  Datos actualizados (debe incluir el id)
     */
    public function update(int $id, array $data): bool
    {
        return $this->dataUserService->updateDataUser(array_merge($data, ['id' => $id]));
    }

    /**
     * Eliminar datos de usuario por ID.
     *
     * @param  int  $id  Identificador único de los datos de usuario
     */
    public function delete(int $id): bool
    {
        return $this->dataUserService->deleteDataUser($id);
    }
}
