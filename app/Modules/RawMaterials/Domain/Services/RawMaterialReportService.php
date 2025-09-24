<?php

namespace App\Modules\RawMaterials\Domain\Services;

use App\Modules\RawMaterials\Domain\Repositories\RawMaterialRepositoryI;

/**
 * Servicio de reportes para materias primas.
 *
 * Encapsula la lógica específica de reportes del módulo RawMaterials.
 * Principios SOLID aplicados: SRP, DIP.
 */
class RawMaterialReportService
{
    public function __construct(
        private RawMaterialRepositoryI $repository
    ) {}

    /**
     * Obtiene el reporte de materias primas.
     *
     * @param  array  $params  Filtros y parámetros
     */
    public function getReport(array $params = []): array
    {
        $filters = $this->prepareFilters($params);
        $data = $this->repository->getMaterialsReport($filters);

        return [
            'title' => 'Reporte de Materias Primas',
            'module' => 'raw_materials',
            'generated_at' => now()->toISOString(),
            'filters_applied' => $filters,
            'total_items' => count($data),
            'summary' => $this->generateSummary($data),
            'data' => $data,
        ];
    }

    /**
     * Obtiene reporte de stock bajo.
     */
    public function getLowStockReport(): array
    {
        $data = $this->repository->getLowStockMaterials();

        return [
            'title' => 'Materiales con Stock Bajo',
            'module' => 'raw_materials',
            'generated_at' => now()->toISOString(),
            'total_items' => count($data),
            'urgent_restock' => array_filter($data, fn ($item) => $item->needsUrgentRestock()),
            'data' => $data,
        ];
    }

    private function prepareFilters(array $params): array
    {
        return array_filter([
            'status' => $params['status'] ?? null,
            'low_stock_only' => $params['low_stock_only'] ?? false,
            'material_name' => $params['material_name'] ?? null,
        ]);
    }

    private function generateSummary(array $data): array
    {
        return [
            'total_materials' => count($data),
            'low_stock_count' => count(array_filter($data, fn ($item) => $item->hasLowStock())),
            'out_of_stock_count' => count(array_filter($data, fn ($item) => $item->needsUrgentRestock())),
            'normal_stock_count' => count(array_filter($data, fn ($item) => ! $item->hasLowStock())),
        ];
    }
}
