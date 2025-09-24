<?php

namespace App\Modules\DataUser\Domain\Repositories;

use App\Modules\DataUser\Domain\Entities\DataUserEntity;

/**
 * Contrato para el repositorio de datos de usuario.
 *
 * Define los métodos que cualquier implementación de repositorio de datos de usuario debe proveer,
 * permitiendo desacoplar la lógica de acceso a datos de la lógica de negocio.
 *
 * Principios SOLID aplicados:
 * - SRP: Solo define la interfaz para persistencia de datos de usuario.
 * - OCP: Puede extenderse con nuevas implementaciones sin modificar el contrato.
 * - LSP: Cualquier implementación puede sustituir esta interfaz sin romper el sistema.
 * - ISP: La interfaz es específica para datos de usuario, no incluye métodos innecesarios.
 * - DIP: Los servicios y casos de uso dependen de esta abstracción, no de una implementación concreta.
 */
interface DataUserRepositoryInterface
{
    /**
     * Obtiene todos los datos de usuarios, opcionalmente filtrados.
     *
     * @param  array  $filters  Filtros de búsqueda (ej: ['status' => 'active'])
     * @return array Lista de entidades de datos de usuario
     */
    public function all(array $filters = []): array;

    /**
     * Busca datos de usuario por su identificador.
     *
     * @param  string  $id  Identificador único de los datos de usuario
     * @return DataUserEntity|null Entidad de datos de usuario o null si no existe
     */
    public function find(string $id): ?DataUserEntity;

    /**
     * Crea nuevos datos de usuario.
     *
     * @param  array  $data  Datos del usuario (campos específicos del módulo DataUser)
     * @return DataUserEntity|null Entidad creada o null si falla
     */
    public function create(array $data): ?DataUserEntity;

    /**
     * Actualiza datos de usuario existentes.
     *
     * @param  array  $data  Datos actualizados (debe incluir el id)
     * @return bool True si la actualización fue exitosa, false en caso contrario
     */
    public function update(array $data): bool;

    /**
     * Elimina datos de usuario por su identificador.
     *
     * @param  string  $id  Identificador único de los datos de usuario
     * @return bool True si la eliminación fue exitosa, false en caso contrario
     */
    public function delete(string $id): bool;
}
