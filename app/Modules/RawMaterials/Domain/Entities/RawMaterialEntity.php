<?php

namespace App\Modules\RawMaterials\Domain\Entities;

/**
 * Entidad de dominio para materia prima.
 *
 * Representa los datos y reglas de negocio de una materia prima en el sistema de producción.
 * Esta entidad pura no depende de ningún framework y encapsula la lógica del dominio.
 *
 * Principios SOLID aplicados:
 * - SRP: Solo gestiona datos y comportamientos de materia prima.
 * - OCP: Puede extenderse sin modificar el código base.
 * - LSP: Subclases pueden sustituir esta clase sin romper el sistema.
 * - ISP: Si implementa interfaces, deben ser específicas.
 * - DIP: Puede depender de abstracciones para mayor flexibilidad.
 *
 * @package App\Modules\RawMaterials\Domain\Entities
 */
class RawMaterialEntity
{
    /**
     * Crea una nueva instancia de materia prima.
     *
     * @param int|null $id Identificador único de la materia prima
     * @param string $name Nombre de la materia prima
     * @param string $code Código único de identificación
     * @param string|null $description Descripción detallada (opcional)
     * @param string $unit_of_measure Unidad de medida (kg, gr, litros, etc.)
     * @param float $stock Cantidad actual en inventario
     * @param float $min_stock Cantidad mínima requerida en stock
     * @param float $is_active Estado activo/inactivo (1.0 = activo, 0.0 = inactivo)
     * @param int|null $created_by ID del usuario que creó el registro
     */
    public function __construct(
        public int|null $id,
        public string $name,
        public string $code,
        public string|null $description,
        public string $unit_of_measure,
        public float $stock,
        public float $min_stock,
        public float $is_active,
        public int|null $created_by,
    ) {}

    /**
     * Indica si la materia prima está activa en el sistema.
     *
     * @return bool True si está activa, false en caso contrario
     */
    public function isActive(): bool
    {
        return $this->is_active === 1.0;
    }

    /**
     * Verifica si el stock actual está por debajo del mínimo requerido.
     *
     * @return bool True si requiere reposición, false si el stock es suficiente
     */
    public function requiresRestock(): bool
    {
        return $this->stock <= $this->min_stock;
    }

    /**
     * Calcula la cantidad necesaria para alcanzar el stock mínimo.
     *
     * @return float Cantidad necesaria para reposición (0 si no necesita)
     */
    public function getRestockQuantity(): float
    {
        return $this->requiresRestock() ? ($this->min_stock - $this->stock) : 0.0;
    }
}

