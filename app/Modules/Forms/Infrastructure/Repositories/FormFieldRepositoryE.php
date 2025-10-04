<?php

namespace App\Modules\Forms\Infrastructure\Repositories;

use App\Models\FormField;
use App\Modules\Forms\Domain\Entities\FormFieldEntity;
use App\Modules\Forms\Domain\Repository\FormFieldRepositoryI;
use Illuminate\Pagination\LengthAwarePaginator;

class FormFieldRepositoryE implements FormFieldRepositoryI
{
    /**
     * Obtiene todos los campos de un formulario, con filtros opcionales.
     */
    public function all(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = FormField::query();

        // Filtrar por formulario
        if (isset($filters['form_id'])) {
            $query->where('form_id', $filters['form_id']);
        }

        // Otros posibles filtros (nombre, tipo, requerido, etc.)
        if (isset($filters['label'])) {
            $query->where('label', 'like', "%{$filters['label']}%");
        }

        return $query->paginate($perPage);
    }

    /**
     * Busca un campo por su ID.
     */
    public function find(int $id): ?FormFieldEntity
    {
        $formField = FormField::find($id);

        // Si no se encontró, devuelve null para evitar el error "null given"
        if (! $formField) {
            return null;
        }

        return FormFieldEntity::fromModel($formField);
    }

    /**
     * Crea un nuevo campo de formulario.
     */
    public function create(FormFieldEntity $formFieldEntity): bool
    {
        // Convertir la entidad a array y crear en la base de datos
        $created = FormField::create($formFieldEntity->toArray());

        return (bool) $created;
    }

    /**
     * Actualiza un campo existente.
     */
    public function update(FormFieldEntity $formFieldEntity): bool
    {
        $formField = FormField::find($formFieldEntity->getId());

        if (! $formField) {
            return false;
        }

        $formField->update($formFieldEntity->toArray());

        return true;
    }

    /**
     * Elimina un campo de formulario.
     */
    public function delete(int $id): bool
    {
        $formField = FormField::find($id);

        if (! $formField) {
            return false;
        }

        $formField->delete();

        return true;
    }
}
