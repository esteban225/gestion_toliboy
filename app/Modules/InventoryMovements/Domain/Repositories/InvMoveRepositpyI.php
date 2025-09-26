<?php

namespace App\Modules\InventoryMovements\Domain\Repositories;

use App\Modules\InventoryMovements\Domain\Entities\InvMoveEntity;

/*

(SRP)Single Responsability Principle
    está interfaz cumple el pimer pincipio solid lo cual dice
    cada clase o interfaz debe tener una sola razon para cambiar
    y debe tener una unica responsabilidad ya que mi interfar la
    ubica responsabilidad que tiene es gestionar el acceso a los
    datos de mi entidad
(ISP) Interface Segregation Principle
    estña interfaz cumple el cuatro pricipio SOLID que dice
    crear interfaces pequeñas y especificas, no una gigante
    que obliga a implementar metodos inutiles

Este es mi contrato el cual dice que se puede hacer más no como hacerlo
*/
interface InvMoveRepositpyI
{
    public function create(InvMoveEntity $entity): InvMoveEntity;

    public function find(int $id): ?InvMoveEntity;

    public function list(array $filters = []): array;

    public function update(InvMoveEntity $entity): bool;

    public function delete(int $id): bool;
}
