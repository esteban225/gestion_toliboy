<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel; // para Excel::download o Excel::store

class ReportsController extends Controller
{
    /**
     * Devuelve los datos de un reporte (vista) por nombre.
     */
    public function report(Request $request, string $reportName)
    {
        $db = DB::getDatabaseName();

        $allowed = [
            'production_summary' => 'v_production_summary',
            'batches_by_status'  => 'v_batches_by_status',
            'current_stock'      => 'v_stock_below_min',
            'inventory_monthly'  => 'v_inventory_monthly_summary',
        ];

        if (! isset($allowed[$reportName])) {
            return response()->json(['message' => 'Report not found'], 404);
        }

        $view = $allowed[$reportName];

        $exists = DB::table('information_schema.views')
            ->where('TABLE_SCHEMA', $db)
            ->where('TABLE_NAME', $view)
            ->exists();

        if (! $exists) {
            return response()->json(['message' => 'Report view does not exist in DB'], 404);
        }

        $query = DB::table($view);

        if ($request->has('date_from')) {
            $query->where('date', '>=', $request->query('date_from'));
        }
        if ($request->has('date_to')) {
            $query->where('date', '<=', $request->query('date_to'));
        }
        if ($request->has('q')) {
            $q = $request->query('q');
            $query->where(function ($qbl) use ($q) {
                $qbl->whereRaw("CAST(id AS CHAR) LIKE ?", ["%{$q}%"])
                    ->orWhereRaw("CAST(raw_material_id AS CHAR) LIKE ?", ["%{$q}%"]);
            });
        }

        $limit = min(2000, (int) $request->query('limit', 500));
        $data  = $query->limit($limit)->get();

        return response()->json([
            'report' => $reportName,
            'view'   => $view,
            'rows'   => $data,
            'count'  => $data->count(),
        ]);
    }

    /**
     * Exporta un reporte en csv, pdf o xlsx.
     * /api/reports/{reportName}/export?format=csv|pdf|xlsx
     */
    public function export(Request $request, string $reportName)
    {
        $format = strtolower($request->query('format', 'csv'));
        $db     = DB::getDatabaseName();

        $allowed = [
            'production_summary' => 'v_production_summary',
            'batches_by_status'  => 'v_batches_by_status',
            'current_stock'      => 'v_current_stock',
            'inventory_monthly'  => 'v_inventory_monthly_summary',
        ];

        if (! isset($allowed[$reportName])) {
            return response()->json(['message' => 'Report not found'], 404);
        }

        $view = $allowed[$reportName];

        $exists = DB::table('information_schema.views')
            ->where('TABLE_SCHEMA', $db)
            ->where('TABLE_NAME', $view)
            ->exists();

        if (! $exists) {
            return response()->json(['message' => 'Report view does not exist in DB'], 404);
        }

        $rows = DB::table($view)->limit(5000)->get();

        // --- EXPORT CSV ---
        if ($format === 'csv') {
            $filename = "{$reportName}-" . date('Ymd_His') . ".csv";

            $headers = [
                'Content-Type'        => 'text/csv; charset=UTF-8',
                'Content-Disposition' => "attachment; filename={$filename}",
            ];

            $callback = function () use ($rows, $reportName) {
                $out = fopen('php://output', 'w');
                fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM UTF-8

                if ($rows->isEmpty()) {
                    fputcsv($out, ['Reporte: ' . ucfirst(str_replace('_', ' ', $reportName))]);
                    fputcsv($out, ['Generado el', date('d/m/Y H:i')]);
                    fputcsv($out, ['No data']);
                    fclose($out);
                    return;
                }

                fputcsv($out, ['Reporte: ' . ucfirst(str_replace('_', ' ', $reportName))]);
                fputcsv($out, ['Generado el', date('d/m/Y H:i')]);
                fputcsv($out, []);

                $first      = (array) $rows->first();
                $headersRow = array_map(fn($col) => ucwords(str_replace('_', ' ', $col)), array_keys($first));
                fputcsv($out, $headersRow);

                foreach ($rows as $row) {
                    fputcsv($out, array_values((array) $row));
                }

                fclose($out);
            };

            return Response::stream($callback, 200, $headers);
        }

        // --- EXPORT PDF ---
        if ($format === 'pdf') {
            if (! class_exists(\Dompdf\Dompdf::class)) {
                return response()->json([
                    'message' => 'PDF export requires dompdf/dompdf. Run: composer require dompdf/dompdf'
                ], 501);
            }

            $styles = '
        <style>
            body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; }
            .header { text-align: center; margin-bottom: 20px; }
            .header h1 { margin: 0; font-size: 22px; color: #004080; } /* Azul */
            .header small { color: #888; }
            table { width: 100%; border-collapse: collapse; margin-top: 10px; }
            thead { background: #004080; color: #fff; } /* Azul oscuro */
            th, td { padding: 8px; border: 1px solid #ccc; text-align: left; }
            tbody tr:nth-child(even) { background: #f2f6ff; } /* Azul claro */
            tfoot td { font-size: 11px; text-align: right; color: #c0392b; /* Rojo */ padding-top: 10px; border: none; }
        </style>
        ';

            $html = $styles . '
        <div class="header">
            <h1>Reporte: ' . ucfirst(str_replace('_', ' ', $reportName)) . '</h1>
            <small>Generado el ' . date('d/m/Y H:i') . '</small>
        </div>
        ';

            $html .= '<table>';
            if ($rows->isNotEmpty()) {
                $first = (array) $rows->first();
                $html .= '<thead><tr>';
                foreach (array_keys($first) as $col) {
                    $html .= '<th>' . e(ucwords(str_replace('_', ' ', $col))) . '</th>';
                }
                $html .= '</tr></thead><tbody>';
                foreach ($rows as $row) {
                    $arr = (array) $row;
                    $html .= '<tr>';
                    foreach ($arr as $cell) {
                        $html .= '<td>' . e((string) $cell) . '</td>';
                    }
                    $html .= '</tr>';
                }
                $html .= '</tbody>';
            } else {
                $html .= '<thead><tr><th>No hay datos</th></tr></thead>';
            }

            $html .= '<tfoot><tr><td colspan="100%">Reporte generado automáticamente — '
                . config('app.name') . '</td></tr></tfoot>';
            $html .= '</table>';

            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();

            return response($dompdf->output(), 200, [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $reportName . '-' . date('Ymd_His') . '.pdf"',
            ]);
        }

        // --- EXPORT XLSX ---
        if ($format === 'xlsx') {
            if (! class_exists(\Maatwebsite\Excel\Facades\Excel::class)) {
                return response()->json([
                    'message' => 'La exportación a XLSX requiere maatwebsite/excel. Ejecuta: composer require maatwebsite/excel'
                ], 501);
            }

            $headings = [];
            if ($rows->isNotEmpty()) {
                $headings = array_map(fn($col) => ucwords(str_replace('_', ' ', $col)), array_keys((array) $rows->first()));
            }

            $export = new class($rows, $headings) implements
                \Maatwebsite\Excel\Concerns\FromCollection,
                \Maatwebsite\Excel\Concerns\WithHeadings,
                \Maatwebsite\Excel\Concerns\WithMapping,
                \Maatwebsite\Excel\Concerns\ShouldAutoSize,
                \Maatwebsite\Excel\Concerns\WithStyles
            {
                private $rows;
                private $headings;

                public function __construct($rows, $headings)
                {
                    $this->rows     = $rows;
                    $this->headings = $headings;
                }

                public function collection()
                {
                    return $this->rows;
                }

                public function headings(): array
                {
                    return $this->headings;
                }

                public function map($row): array
                {
                    $data = (array) $row;
                    if (isset($data['created_at'])) {
                        $data['created_at'] = date('d/m/Y H:i', strtotime($data['created_at']));
                    }
                    return $data;
                }

                public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
                {
                    // Encabezados (azul)
                    $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1')->applyFromArray([
                        'font' => [
                            'bold'  => true,
                            'color' => ['argb' => 'FFFFFFFF'],
                        ],
                        'fill' => [
                            'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'startColor' => ['argb' => 'FF004080'], // Azul oscuro
                        ],
                    ]);

                    // Texto general
                    $sheet->getStyle('A2:' . $sheet->getHighestColumn() . $sheet->getHighestRow())->applyFromArray([
                        'font' => [
                            'size'  => 10,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ]);

                    // Totales (última fila en rojo)
                    $lastRow = $sheet->getHighestRow();
                    $sheet->getStyle('A' . $lastRow . ':' . $sheet->getHighestColumn() . $lastRow)->applyFromArray([
                        'font' => [
                            'bold'  => true,
                            'color' => ['argb' => 'FF000000'],
                        ],
                        'fill' => [
                            'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'startColor' => ['argb' => 'FFFFFFFF'], // Blanco
                        ],
                    ]);
                }
            };

            $filename = "{$reportName}-" . date('Ymd_His') . ".xlsx";
            return \Maatwebsite\Excel\Facades\Excel::download($export, $filename);
        }

        return response()->json(['message' => 'Unsupported format'], 400);
    }
}
