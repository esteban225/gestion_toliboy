<?php

namespace App\Modules\Reports\Domain\Services;

use App\Modules\RawMaterials\Domain\Services\RawMaterialReportService;

// use App\Modules\Inventory\Domain\Services\InventoryReportService;

/**
 * Servicio agregador de reportes.
 *
 * Combina datos de múltiples módulos para reportes cruzados.
 */
class ReportAggregatorService
{
    public function __construct(
        private RawMaterialReportService $rawMaterialService,
        // private InventoryReportService $inventoryService
    ) {}

    /**
     * Genera dashboard ejecutivo combinando datos de múltiples módulos.
     */
    public function getDashboardData(array $params = []): array
    {
        $rawMaterialsData = $this->rawMaterialService->getReport($params);
        // $inventoryData = $this->inventoryService->getReport($params);

        return [
            'title' => 'Dashboard Ejecutivo',
            'module' => 'dashboard',
            'generated_at' => now()->toISOString(),
            'sections' => [
                'raw_materials' => [
                    'title' => 'Materias Primas',
                    'summary' => $rawMaterialsData['summary'] ?? [],
                    'alerts' => $this->getAlerts($rawMaterialsData),
                ],
                'inventory' => [
                    'title' => 'Inventario General',
                    'summary' => $inventoryData['summary'] ?? [],
                ],
            ],
            'global_alerts' => $this->getGlobalAlerts($rawMaterialsData
                // , $inventoryData
            ),
        ];
    }

    private function getAlerts(array $data): array
    {
        return [
            'low_stock' => $data['summary']['low_stock_count'] ?? 0,
            'out_of_stock' => $data['summary']['out_of_stock_count'] ?? 0,
        ];
    }

    private function getGlobalAlerts(array ...$reports): array
    {
        // Lógica para alertas globales
        return [
            'critical_items' => 0,
            'warnings' => 0,
            'info' => 0,
        ];
    }
}
