<?php

namespace App\Modules\Reports\Infrastructure\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;

class ReportsRepository
{
    public function fetchReport(string $reportName, array $filters = []): array
    {
        $db = DB::getDatabaseName();

        // Verificar que la vista exista
        $exists = DB::table('information_schema.views')
            ->where('TABLE_SCHEMA', $db)
            ->where('TABLE_NAME', $reportName)
            ->exists();

        if (! $exists) {
            return [];
        }

        $query   = DB::table($reportName);
        $columns = DB::getSchemaBuilder()->getColumnListing($reportName);

        // Filtros dinámicos
        if (! empty($filters['date_from']) && in_array('date', $columns)) {
            $query->where('date', '>=', $filters['date_from']);
        }
        if (! empty($filters['date_to']) && in_array('date', $columns)) {
            $query->where('date', '<=', $filters['date_to']);
        }
        if (! empty($filters['q'])) {
            $q = $filters['q'];
            $query->where(function ($qb) use ($q, $columns) {
                if (in_array('id', $columns)) {
                    $qb->whereRaw("CAST(id AS CHAR) LIKE ?", ["%{$q}%"]);
                }
                if (in_array('raw_material_id', $columns)) {
                    $qb->orWhereRaw("CAST(raw_material_id AS CHAR) LIKE ?", ["%{$q}%"]);
                }
            });
        }

        $limit = min(5000, (int) ($filters['limit'] ?? 500));
        return $query->limit($limit)->get()->toArray();
    }

    public function export(string $reportName, string $format, array $filters = [])
    {
        $rows     = $this->fetchReport($reportName, $filters);
        $filename = "{$reportName}-" . date('Ymd_His');

        // --- CSV ---
        if ($format === 'csv') {
            $headers = [
                'Content-Type'        => 'text/csv; charset=UTF-8',
                'Content-Disposition' => "attachment; filename={$filename}.csv",
            ];

            $callback = function () use ($rows, $reportName) {
                $out = fopen('php://output', 'w');
                fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM UTF-8

                if (empty($rows)) {
                    fputcsv($out, ['Reporte: ' . ucfirst(str_replace('_', ' ', $reportName))]);
                    fputcsv($out, ['Generado el', date('d/m/Y H:i')]);
                    fputcsv($out, ['No data']);
                    fclose($out);
                    return;
                }

                fputcsv($out, ['Reporte: ' . ucfirst(str_replace('_', ' ', $reportName))]);
                fputcsv($out, ['Generado el', date('d/m/Y H:i')]);
                fputcsv($out, []);

                $first = (array) $rows[0];
                $headersRow = array_map(fn($col) => ucwords(str_replace('_', ' ', $col)), array_keys($first));
                fputcsv($out, $headersRow);

                foreach ($rows as $row) {
                    fputcsv($out, array_values((array) $row));
                }

                fclose($out);
            };

            return Response::stream($callback, 200, $headers);
        }

        // --- XLSX ---
        if ($format === 'xlsx') {
            if (! class_exists(\Maatwebsite\Excel\Facades\Excel::class)) {
                return response()->json(['message' => 'XLSX requires maatwebsite/excel'], 501);
            }

            $arrayData = array_map(fn($r) => (array) $r, $rows);
            $headings  = ! empty($arrayData)
                ? array_map(fn($col) => ucwords(str_replace('_', ' ', $col)), array_keys($arrayData[0]))
                : [];

            $export = new class($arrayData, $headings) implements
                \Maatwebsite\Excel\Concerns\FromArray,
                \Maatwebsite\Excel\Concerns\WithHeadings,
                \Maatwebsite\Excel\Concerns\ShouldAutoSize,
                \Maatwebsite\Excel\Concerns\WithStyles
            {
                private array $data;
                private array $headings;

                public function __construct(array $data, array $headings)
                {
                    $this->data     = $data;
                    $this->headings = $headings;
                }

                public function array(): array
                {
                    return $this->data;
                }

                public function headings(): array
                {
                    return $this->headings;
                }

                public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
                {
                    $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1')->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                        'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                   'startColor' => ['argb' => 'FF004080']],
                    ]);

                    for ($i = 2; $i <= $sheet->getHighestRow(); $i++) {
                        if ($i % 2 === 0) {
                            $sheet->getStyle("A{$i}:" . $sheet->getHighestColumn() . "{$i}")
                                ->applyFromArray(['fill' => [
                                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                    'startColor' => ['argb' => 'FFF2F2F2'],
                                ]]);
                        }
                    }

                    $lastRow = $sheet->getHighestRow();
                    $sheet->getStyle("A{$lastRow}:" . $sheet->getHighestColumn() . $lastRow)->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['argb' => 'FFC0392B']],
                    ]);
                }
            };

            return Excel::download($export, $filename . '.xlsx');
        }

        // --- PDF ---
        if ($format === 'pdf') {
            if (! class_exists(\Dompdf\Dompdf::class)) {
                return response()->json(['message' => 'PDF requires dompdf/dompdf'], 501);
            }

            $styles = '
            <style>
                body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; }
                .header { text-align: center; margin-bottom: 20px; }
                .header h1 { margin: 0; font-size: 22px; color: #004080; }
                .header small { color: #888; }
                table { width: 100%; border-collapse: collapse; margin-top: 10px; }
                thead { background: #004080; color: #fff; }
                th, td { padding: 8px; border: 1px solid #ccc; text-align: left; }
                tbody tr:nth-child(even) { background: #f2f6ff; }
                tfoot td { font-size: 11px; text-align: right; color: #c0392b; padding-top: 10px; border: none; }
            </style>
            ';

            $html = $styles . '
            <div class="header">
                <h1>Reporte: ' . ucfirst(str_replace('_', ' ', $reportName)) . '</h1>
                <small>Generado el ' . date('d/m/Y H:i') . '</small>
            </div>';

            $html .= '<table>';
            if (! empty($rows)) {
                $first = (array) $rows[0];
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

            $html .= '<tfoot><tr><td colspan="100%">Reporte generado automáticamente — ' . config('app.name') . '</td></tr></tfoot>';
            $html .= '</table>';

            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();

            return response($dompdf->output(), 200, [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => "attachment; filename=\"{$filename}.pdf\"",
            ]);
        }

        return response()->json(['message' => 'Unsupported format'], 400);
    }
}
