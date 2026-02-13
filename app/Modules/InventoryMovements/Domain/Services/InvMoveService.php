<?php

namespace App\Modules\InventoryMovements\Domain\Services;

use App\Modules\InventoryMovements\Application\UseCases\InvNotificationUseCase;
use App\Modules\InventoryMovements\Domain\Entities\InvMoveEntity;
use App\Modules\InventoryMovements\Domain\Repositories\InvMoveRepositoryI;
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

class InvMoveService
{
    private InvMoveRepositoryI $repositoy;

    private InvNotificationUseCase $notificationUseCase;

    public function __construct(InvMoveRepositoryI $repositoy, InvNotificationUseCase $notificationUseCase)
    {
        $this->repositoy = $repositoy;
        $this->notificationUseCase = $notificationUseCase;
    }

    public function list(array $filters = [], int $perpage = 15): LengthAwarePaginator
    {
        return $this->repositoy->list($filters, $perpage);
    }

    public function find(int $id): ?InvMoveEntity
    {
        return $this->repositoy->find($id);
    }

    public function create(InvMoveEntity $entity): ?InvMoveEntity
    {
        $this->notificationUseCase->execute($entity->toArray());
        $created = $this->repositoy->create($entity);
        // Si es movimiento de entrada, aumentar stock
        if ($entity->isInbound()) {
            $this->repositoy->increaseStock($entity->getRawMaterialId(), $entity->getQuantity());
        }
        return $created;
    }
    public function increaseStock(int $itemId, float $qty): void
    {
        $this->repositoy->increaseStock($itemId, $qty);
    }

    public function update(InvMoveEntity $entity): bool
    {
        return $this->repositoy->update($entity);
    }

    public function delete(int $id): bool
    {
        return $this->repositoy->delete($id);
    }

    public function reduceStock(int $itemId, float $qty): void
    {
        $this->repositoy->reduceStock($itemId, $qty);
    }
}
