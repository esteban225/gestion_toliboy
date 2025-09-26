<?php

namespace App\Modules\InventoryMovements\Domain\Services;

use App\Modules\InventoryMovements\Domain\Entities\InvMoveEntity;
use App\Modules\InventoryMovements\Domain\Repositories\InvMoveRepositoryI;

/*
está clase cumple dos pricipios SOLID:

(SRP) single Responsability Principle
    está clase solo tiene una unica responsabilidad
    y es implementar la logica de negico

(DIP) Dependency Inversion Pronciple

    establece que modulos de alto nivel no dependan de modulos
    de bajo nivel (implementaciones), si no que deben depender
    de abstracciones (invercion de dependencias)
        - en lugal que el servicio cree una instancia del repositorio
        es inyectado a travez del constructor (inyeccion de dependencias)

si elimino un metodo de mi servicio no rompe el codigo ya que no depende
del repositorio solo depende de una simple abstaccion mi servicio es el
jefe que decide si le sirve el metodo o no, así se simpliifa la estructura
del codigo con metodos necesario y no redundantes
*/

class InvMoveService
{
    private InvMoveRepositoryI $repositoy;

    public function __construct(InvMoveRepositoryI $repositoy)
    {
        $this->repositoy = $repositoy;
    }

    public function list(array $filters = []): array
    {
        return $this->repositoy->list($filters);
    }

    public function find(string $id): ?InvMoveEntity
    {
        return $this->repositoy->find($id);
    }

    public function create(InvMoveEntity $entity): ?InvMoveEntity
    {

        return $this->repositoy->create($entity);
    }

    public function update(InvMoveEntity $entity): bool
    {
        return $this->repositoy->update($entity);
    }

    public function delete(int $id): bool
    {
        return $this->repositoy->delete($id);
    }
}
