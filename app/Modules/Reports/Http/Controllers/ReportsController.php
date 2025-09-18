<?php

namespace App\Modules\Reports\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Reports\UseCases\ExportReportUseCase;

class ReportsController extends Controller
{
    private ExportReportUseCase $useCase;

    public function __construct(ExportReportUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function report(Request $request, string $reportName)
    {
        return $this->useCase->handle($request, $reportName);
    }

    public function export(Request $request, string $reportName)
    {
        return $this->useCase->export($request, $reportName);
    }
}
