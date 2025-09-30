<?php

/**
 * Controlador para la gestión y exportación de reportes.
 * Permite generar reportes dinámicos y personalizados a partir de datos recibidos.
 *
 * Métodos:
 * - report: Genera un reporte en base al nombre y parámetros recibidos.
 * - export: Exporta el reporte en el formato solicitado.
 * - exportReport: Exporta un reporte a partir de datos validados (pueden venir en JSON).
 */

namespace App\Modules\Reports\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Reports\Http\Requests\ReportsRequest;
use App\Modules\Reports\UseCases\ExportReportUseCase;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    /**
     * Caso de uso para la exportación de reportes.
     */
    private ExportReportUseCase $useCase;

    /**
     * Constructor.
     */
    public function __construct(ExportReportUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    /**
     * Genera un reporte en base al nombre y parámetros recibidos.
     */
    public function report(Request $request, string $reportName)
    {
        return $this->useCase->handle($request, $reportName);
    }

    /**
     * Exporta el reporte en el formato solicitado.
     */
    public function export(Request $request, string $reportName)
    {
        return $this->useCase->export($request, $reportName);
    }

    /**
     * Exporta un reporte a partir de datos validados (pueden venir en JSON).
     */
    public function exportReport(ReportsRequest $request)
    {
        $validated = $request->validated();

        if (! $validated) {
            return response()->json(['message' => 'Invalid data provided'], 400);
        }

        return $this->useCase->exportReport($request);
    }
}
