<?php

namespace App\Modules\Reports\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Reports\Application\UseCases\GenerateReportUseCase;
use Illuminate\Http\Request;

/**
 * Controlador centralizado de reportes.
 *
 * Expone endpoints unificados para todos los reportes del sistema.
 *
 * @group Reports
 *
 * @description Endpoints para generar y exportar reportes del sistema.
 */
class ReportsController extends Controller
{
    public function __construct(
        private GenerateReportUseCase $generateReport
    ) {}

    /**
     * Listar reportes disponibles.
     *
     * @response 200 {
     *   "success": true,
     *   "reports": {
     *     "raw_materials": "Reporte de Materias Primas",
     *     "inventory": "Reporte de Inventario"
     *   }
     * }
     */
    public function index()
    {
        return response()->json([
            'success' => true,
            'message' => 'Reportes disponibles',
            'reports' => [
                'raw_materials' => 'Reporte de Materias Primas',
                'raw_materials_low_stock' => 'Materiales con Stock Bajo',
                'inventory' => 'Reporte de Inventario',
                'dashboard' => 'Dashboard Ejecutivo',
            ],
        ]);
    }

    /**
     * Generar reporte de materias primas.
     *
     * @queryParam format string Formato de exportación: json, pdf, excel
     * @queryParam status string Filtrar por estado
     * @queryParam low_stock_only boolean Solo materiales con stock bajo
     *
     * @response 200 {
     *   "success": true,
     *   "data": {
     *     "title": "Reporte de Materias Primas",
     *     "total_items": 10
     *   }
     * }
     */
    public function rawMaterials(Request $request)
    {
        return $this->generateReport->handle('raw_materials', $request->all());
    }

    /**
     * Generar reporte de stock bajo.
     *
     * @queryParam format string Formato de exportación
     */
    public function rawMaterialsLowStock(Request $request)
    {
        return $this->generateReport->handle('raw_materials_low_stock', $request->all());
    }

    /**
     * Generar reporte de inventario.
     */
    public function inventory(Request $request)
    {
        return $this->generateReport->handle('inventory', $request->all());
    }

    /**
     * Generar dashboard ejecutivo.
     *
     * @response 200 {
     *   "success": true,
     *   "data": {
     *     "title": "Dashboard Ejecutivo",
     *     "sections": {}
     *   }
     * }
     */
    public function dashboard(Request $request)
    {
        return $this->generateReport->handle('dashboard', $request->all());
    }
}
