<?php

namespace App\Modules\Forms\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Forms\Infrastructure\Repositories\FormsReportRepository;
use Dedoc\Scramble\Attributes\Group;
use Dompdf\Dompdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;

/**
 * Controlador para la generación de reportes de formularios.
 *
 * Este controlador maneja la generación y exportación de reportes relacionados con los formularios
 * y sus respuestas en diferentes formatos como PDF.
 *
 * Características principales:
 *
 * - Generación de reportes PDF con datos de formularios
 * - Soporte para filtrado por rangos de fechas
 * - Personalización del formato y diseño de reportes
 * - Almacenamiento opcional de reportes generados
 * - Descarga directa de reportes
 */
#[Group(name: 'Módulo de Formularios: Generación de reportes', weight: 12)]
class FormReportsController extends Controller
{
    /**
     * Constructor del controlador.
     * Inicializa el controlador con el repositorio necesario para acceder a los datos de reportes.
     *
     * @param  FormsReportRepository  $repo  Repositorio para acceder a los datos de formularios y generar reportes
     */
    public function __construct(private FormsReportRepository $repo) {}

    /**
     * Genera y descarga un reporte PDF para un formulario específico.
     *
     * Este endpoint genera un reporte detallado en formato PDF con la siguiente funcionalidad:
     *
     * Características del reporte:
     *
     * - Formato de página A4 en orientación horizontal
     * - Encabezado con título y fecha de generación
     * - Datos tabulares con encabezados personalizados
     * - Paginación automática
     * - Filtrado por rango de fechas
     *
     * Proceso de generación:
     *
     * - Recupera los datos del formulario y sus respuestas
     * - Procesa y formatea los datos para el reporte
     * - Genera el PDF usando Dompdf
     * - Guarda una copia en el almacenamiento local (opcional)
     * - Permite descarga directa del archivo
     *
     * @endpoint GET /api/forms/{formId}/reports/pdf
     *
     * @param  Request  $request  Solicitud con filtros de fecha (date_from, date_to)
     * @param  int  $formId  Identificador único del formulario
     * @return mixed Respuesta con el archivo PDF para descarga
     */
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

        return response()->streamDownload(fn () => print $dompdf->output(), $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
