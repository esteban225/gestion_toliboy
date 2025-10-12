<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>{{ $title ?? 'Reporte de formulario' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            font-size: 11px;
            color: #1a202c;
            line-height: 1.6;
            background: #ffffff;
            padding: 20px;
        }

        .container {
            max-width: 100%;
            background: #ffffff;
            border: 1px solid #e2e8f0;
        }

        header {
            background: #1e40af;
            padding: 24px 28px;
            color: #ffffff;
            border-bottom: 4px solid #1e3a8a;
        }

        h2 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 6px;
            letter-spacing: -0.3px;
        }

        .meta {
            font-size: 12px;
            color: #e0e7ff;
            font-weight: 500;
            margin-top: 4px;
        }

        .meta::before {
            content: "📅 ";
            font-size: 13px;
        }

        .table-wrapper {
            padding: 24px 28px 28px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #cbd5e1;
        }

        thead {
            background: #f1f5f9;
        }

        thead th {
            color: #1e293b;
            font-weight: 700;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 14px 14px;
            text-align: left;
            border-bottom: 2px solid #94a3b8;
            border-right: 1px solid #e2e8f0;
        }

        thead th:last-child {
            border-right: none;
        }

        tbody tr {
            border-bottom: 1px solid #e2e8f0;
        }

        tbody tr:last-child {
            border-bottom: none;
        }

        tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        tbody tr:nth-child(odd) {
            background: #ffffff;
        }

        td {
            padding: 12px 14px;
            color: #475569;
            font-size: 10px;
            vertical-align: top;
            border-right: 1px solid #f1f5f9;
        }

        td:last-child {
            border-right: none;
        }

        td:first-child,
        th:first-child {
            padding-left: 20px;
        }

        td:last-child,
        th:last-child {
            padding-right: 20px;
        }

        .empty-state {
            text-align: center;
            padding: 48px 24px;
            color: #94a3b8;
            background: #f8fafc;
        }

        .empty-state::before {
            content: "📋";
            display: block;
            font-size: 36px;
            margin-bottom: 12px;
        }

        .empty-state-text {
            font-size: 13px;
            font-weight: 500;
            color: #64748b;
            font-style: italic;
        }

        /* Mejor legibilidad en PDF */
        @page {
            margin: 15mm;
        }

        /* Evitar cortes de página en filas */
        tbody tr {
            page-break-inside: avoid;
        }

        thead {
            display: table-header-group;
        }

        tfoot {
            display: table-footer-group;
        }
    </style>
</head>

<body>
    <div class="container">
        <header>
            <h2>{{ $title ?? 'Reporte de formulario' }}</h2>
            <div class="meta">Generado: {{ $generated_at ?? now() }}</div>
        </header>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        @foreach ($headings as $head)
                            <th>{{ $head }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $row)
                        <tr>
                            @foreach ($row as $cell)
                                @php
                                    $display = $cell;
                                    if (is_string($cell)) {
                                        $decoded = json_decode($cell, true);
                                        if (json_last_error() === JSON_ERROR_NONE) {
                                            if (is_array($decoded)) {
                                                $display = implode(', ', array_map(fn($v) => (string) $v, $decoded));
                                            } else {
                                                $display = (string) $decoded;
                                            }
                                        }
                                    }
                                    if (is_string($display) && strlen($display) > 10000) {
                                        $display = substr($display, 0, 10000) . '...';
                                    }
                                @endphp
                                <td>{{ $display }}</td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ max(1, count($headings)) }}" class="empty-state">
                                <div class="empty-state-text">No hay datos disponibles</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
