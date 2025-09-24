<?php

namespace App\Modules\Reports\Application\UseCases;

use App\Modules\RawMaterials\Domain\Services\RawMaterialReportService;
// use App\Modules\Inventory\Domain\Services\InventoryReportService;
use App\Modules\Reports\Domain\Services\ReportAggregatorService;
use App\Modules\Reports\Domain\Services\ReportExportService;
use InvalidArgumentException;

/**
 * Caso de uso para generar reportes.
 *
 * Orquesta la generación de reportes desde diferentes módulos.
 * Principios SOLID: SRP, OCP, DIP.
 */
class GenerateReportUseCase
{
    public function __construct(
        private RawMaterialReportService $rawMaterialService,
        // private InventoryReportService $inventoryService,
        private ReportExportService $exportService,
        private ReportAggregatorService $aggregatorService
    ) {}

    /**
     * Genera un reporte específico.
     *
     * @param  string  $reportType  Tipo de reporte
     * @param  array  $params  Parámetros y filtros
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function handle(string $reportType, array $params)
    {
        $data = $this->getData($reportType, $params);
        $format = $params['format'] ?? 'json';

        return match ($format) {
            'pdf' => $this->exportService->toPDF($data, $reportType),
            'excel' => $this->exportService->toExcel($data, $reportType),
            'json' => response()->json([
                'success' => true,
                'message' => "Reporte {$reportType} generado exitosamente",
                'data' => $data,
            ])
        };
    }

    /**
     * Obtiene los datos según el tipo de reporte.
     */
    private function getData(string $reportType, array $params): array
    {
        return match ($reportType) {
            'raw_materials' => $this->rawMaterialService->getReport($params),
            'raw_materials_low_stock' => $this->rawMaterialService->getLowStockReport(),
            // 'inventory' => $this->inventoryService->getReport($params),
            'dashboard' => $this->aggregatorService->getDashboardData($params),
            default => throw new InvalidArgumentException("Reporte no encontrado: {$reportType}")
        };
    }
}
