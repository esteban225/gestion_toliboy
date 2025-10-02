<?php

namespace App\Modules\InventoryMovements\Application\UseCases;

use App\Modules\InventoryMovements\Domain\Entities\InvMoveEntity;
use App\Modules\InventoryMovements\Domain\Services\InvMoveService;
use Illuminate\Pagination\LengthAwarePaginator;

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

class InvMoveUseCase
{
    private InvMoveService $service;

    public function __construct(InvMoveService $service)
    {
        return $this->service = $service;
    }

    public function list(array $filter = [], int $perpage = 15): LengthAwarePaginator
    {
        return $this->service->list($filter, $perpage);
    }

    public function find(int $id)
    {
        return $this->service->find($id);
    }

    public function create(InvMoveEntity $entity): ?InvMoveEntity
    {
        return $this->service->create($entity);
    }

    public function update(InvMoveEntity $entity): bool
    {
        return $this->service->update($entity);
    }

    public function delete(int $id): bool
    {
        return $this->service->delete($id);
    }

    public function reduceStock(int $itemId, float $qty)
    {
        return $this->service->reduceStock($itemId, $qty);
    }
}
