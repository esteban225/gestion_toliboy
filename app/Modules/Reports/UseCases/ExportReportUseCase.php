<?php

namespace App\Modules\Reports\UseCases;

use App\Modules\Reports\Http\Requests\ReportsRequest;
use App\Modules\Reports\Infrastructure\Repositories\ReportsRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExportReportUseCase
{
    private ReportsRepository $repo;

    public function __construct(ReportsRepository $repo)
    {
        $this->repo = $repo;
    }

    public function handle(Request $request, string $reportName): JsonResponse
    {
        $rows = $this->repo->fetchReport($reportName, $request->query());

        return response()->json([
            'report' => $reportName,
            'rows' => $rows,
            'count' => count($rows),
        ]);
    }

    public function export(Request $request, string $reportName)
    {
        $format = strtolower($request->query('format', 'csv'));

        return $this->repo->export($reportName, $format, $request->query());
    }

    public function exportReport(ReportsRequest $request)
    {
        $validated = $request->validated();

        if (! $validated) {
            return response()->json(['message' => 'Invalid data provided'], 400);
        }

        $rows = $validated['rows'];
        $headings = $validated['headings'];
        $title = $validated['title'];

        // Puedes obtener el formato desde la request si lo deseas
        $format = $request->get('format');

        return $this->repo->exportBlade($rows, $headings, $title, $format);
    }
}
