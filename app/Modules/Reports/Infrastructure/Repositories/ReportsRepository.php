<?php

namespace App\Modules\Reports\Infrastructure\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;

class ReportsRepository
{
    protected array $allowed = [
        'production_summary' => 'v_production_summary',
        'batches_by_status'  => 'v_batches_by_status',
        'current_stock'      => 'v_current_stock',
        'inventory_monthly'  => 'v_inventory_monthly_summary',
    ];

    public function fetchReport(string $reportName, array $filters = []): array
    {
        if (! isset($this->allowed[$reportName])) {
            return [];
        }

        $view = $this->allowed[$reportName];
        $query = DB::table($view);

        if (! empty($filters['date_from'])) {
            $query->where('date', '>=', $filters['date_from']);
        }
        if (! empty($filters['date_to'])) {
            $query->where('date', '<=', $filters['date_to']);
        }
        if (! empty($filters['q'])) {
            $q = $filters['q'];
            $query->where(function ($qb) use ($q) {
                $qb->whereRaw("CAST(id AS CHAR) LIKE ?", ["%{$q}%"])
                   ->orWhereRaw("CAST(raw_material_id AS CHAR) LIKE ?", ["%{$q}%"]);
            });
        }

        $limit = min(5000, (int) ($filters['limit'] ?? 500));
        return $query->limit($limit)->get()->toArray();
    }

    public function export(string $reportName, string $format, array $filters = [])
    {
        $rows = $this->fetchReport($reportName, $filters);
        $filename = "{$reportName}-" . date('Ymd_His');

        if ($format === 'csv') {
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => "attachment; filename={$filename}.csv",
            ];

            $callback = function () use ($rows, $reportName) {
                $out = fopen('php://output', 'w');
                // BOM para Excel
                fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));

                if (empty($rows)) {
                    fputcsv($out, ['No data']);
                    fclose($out);
                    return;
                }

                $first = (array) $rows[0];
                fputcsv($out, array_keys($first));
                foreach ($rows as $row) {
                    fputcsv($out, array_values((array) $row));
                }
                fclose($out);
            };

            return Response::stream($callback, 200, $headers);
        }

        if ($format === 'pdf') {
            if (! class_exists(\Dompdf\Dompdf::class)) {
                return response()->json(['message' => 'PDF requires dompdf/dompdf'], 501);
            }

            $html = '<h3>' . e(str_replace('_', ' ', $reportName)) . '</h3>';
            $html .= '<table border="1" cellpadding="4" cellspacing="0" style="border-collapse:collapse;width:100%">';
            if (! empty($rows)) {
                $first = (array) $rows[0];
                $html .= '<thead><tr>';
                foreach (array_keys($first) as $col) {
                    $html .= '<th>' . e(ucwords(str_replace('_', ' ', $col))) . '</th>';
                }
                $html .= '</tr></thead><tbody>';
                foreach ($rows as $row) {
                    $html .= '<tr>';
                    foreach ((array) $row as $cell) {
                        $html .= '<td>' . e((string) $cell) . '</td>';
                    }
                    $html .= '</tr>';
                }
                $html .= '</tbody>';
            } else {
                $html .= '<tr><td>No data</td></tr>';
            }
            $html .= '</table>';

            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();

            return response($dompdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '.pdf"',
            ]);
        }

        if ($format === 'xlsx') {
            if (! class_exists(\Maatwebsite\Excel\Excel::class) && ! class_exists(\Maatwebsite\Excel\Facades\Excel::class)) {
                return response()->json(['message' => 'XLSX requires maatwebsite/excel'], 501);
            }

            $arrayData = array_map(fn($r) => (array) $r, $rows);
            $headings = ! empty($arrayData) ? array_keys($arrayData[0]) : [];

            $export = new class($arrayData, $headings) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {
                private array $data;
                private array $headings;
                public function __construct(array $data, array $headings) { $this->data = $data; $this->headings = $headings; }
                public function array(): array { return $this->data; }
                public function headings(): array { return $this->headings; }
            };

            return Excel::download($export, $filename . '.xlsx');
        }

        return response()->json(['message' => 'Unsupported format'], 400);
    }
}
