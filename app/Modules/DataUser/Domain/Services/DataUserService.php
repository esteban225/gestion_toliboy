<?php

namespace App\Modules\DataUser\Domain\Services;

use App\Modules\DataUser\Domain\Entities\DataUserEntity;
use App\Modules\DataUser\Domain\Repositories\DataUserRepositoryInterface;

/**
 * Servicio de dominio para gestión de datos de usuario.
 *
 * Encapsula la lógica de negocio relacionada con los datos adicionales de usuario
 * y delega la persistencia al repositorio correspondiente.
 *
 * @package App\Modules\DataUser\Domain\Services
 */
class DataUserService
{
    /**
     * Constructor.
     *
     * @param DataUserRepositoryInterface $data_user_repository Repositorio de datos de usuario
     */
    public function __construct(private DataUserRepositoryInterface $data_user_repository) {}

    /**
     * Listar datos de usuario con filtros opcionales.
     *
     * @param array $filters Filtros de búsqueda (ej: ['user_id' => 5])
     * @return DataUserEntity[]|array Lista de entidades de datos de usuario
     */
    public function listDataUsers(array $filters = [])
    {
        $data = $this->data_user_repository->all($filters);
        return $data;
    }

    /**
     * Obtener datos de usuario por su identificador.
     *
     * @param string $id Identificador único de los datos de usuario
     * @return DataUserEntity|null Entidad de datos de usuario o null si no existe
     */
    public function getDataUser(string $id)
    {
        $data = $this->data_user_repository->find($id);
        return $data;
    }

    /**
     * Crear nuevos datos de usuario.
     *
     * @param array $data Datos del usuario (num_phone, address, emergency_contact, etc.)
     * @return DataUserEntity|null Entidad creada o null si falla
     */
    public function createDataUser(array $data): ?DataUserEntity
    {
        return $this->data_user_repository->create($data);
    }

    /**
     * Actualizar datos de usuario existentes.
     *
     * @param array $data Datos actualizados (debe incluir el id)
     * @return bool True si la actualización fue exitosa, false en caso contrario
     */
    public function updateDataUser(array $data): bool
    {
        return $this->data_user_repository->update($data);
    }

    /**
     * Eliminar datos de usuario por su identificador.
     *
     * @param string $id Identificador único de los datos de usuario
     * @return bool True si la eliminación fue exitosa, false en caso contrario
     */
    public function deleteDataUser(string $id): bool
    {
        return $this->data_user_repository->delete($id);
    }
}