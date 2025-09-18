<?php


namespace App\Modules\Reports\UseCases;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Modules\Reports\Infrastructure\Repositories\ReportsRepository;

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
}
