<?php

namespace App\Modules\Forms\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Forms\Infrastructure\Repositories\FormsReportRepository;
use Dompdf\Dompdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;

class FormReportsController extends Controller
{
    public function __construct(private FormsReportRepository $repo) {}

    public function pdf(Request $request, int $formId)
    {
        $data = $this->repo->fetchFormReport($formId, $request->only(['date_from', 'date_to']), 2000);

        $html = View::make('reports.form_report', [
            'title' => 'Reporte formulario #'.$formId,
            'headings' => $data['headings'],
            'rows' => $data['rows'],
            'generated_at' => now(),
        ])->render();

        $dompdf = new Dompdf;
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $filename = "form_{$formId}_".now()->format('Ymd_His').'.pdf';
        // opcional: guardarlo y devolver URL
        Storage::disk('local')->put("reports/{$filename}", $dompdf->output());

        return response()->streamDownload(fn () => print ($dompdf->output()), $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
