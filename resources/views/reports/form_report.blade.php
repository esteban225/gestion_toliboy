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
            font-size: 10px;
            color: #1a202c;
            line-height: 1.4;
            background: #ffffff;
            padding: 10px;
        }

        .container {
            max-width: 100%;
            background: #ffffff;
            border: 1px solid #e2e8f0;
        }

        header {
            background: #1e40af;
            padding: 16px 20px;
            color: #ffffff;
            border-bottom: 3px solid #1e3a8a;
        }

        h2 {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 4px;
            letter-spacing: -0.3px;
            word-break: break-word;
            overflow-wrap: break-word;
        }

        .meta {
            font-size: 9px;
            color: #e0e7ff;
            font-weight: 500;
            margin-top: 3px;
        }

        .meta::before {
            content: "📅 ";
            font-size: 10px;
        }

        .table-wrapper {
            padding: 16px 20px 20px;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #cbd5e1;
            table-layout: auto;
        }

        thead {
            background: #f1f5f9;
        }

        thead th {
            color: #1e293b;
            font-weight: 700;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            padding: 10px 8px;
            text-align: left;
            border-bottom: 2px solid #94a3b8;
            border-right: 1px solid #e2e8f0;
            word-break: break-word;
            overflow-wrap: break-word;
            hyphens: auto;
            white-space: normal;
        }

        thead th:last-child {
            border-right: none;
        }

        tbody tr {
            border-bottom: 1px solid #e2e8f0;
            page-break-inside: avoid;
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
            padding: 8px 8px;
            color: #475569;
            font-size: 9px;
            vertical-align: top;
            border-right: 1px solid #f1f5f9;
            word-break: break-word;
            overflow-wrap: break-word;
            hyphens: auto;
            white-space: normal;
            max-width: 200px;
        }

        td:last-child {
            border-right: none;
        }

        td:first-child,
        th:first-child {
            padding-left: 12px;
        }

        td:last-child,
        th:last-child {
            padding-right: 12px;
        }

        /* Clases de ancho adaptativo */
        td.narrow {
            max-width: 80px;
            min-width: 50px;
        }

        td.medium {
            max-width: 150px;
            min-width: 100px;
        }

        td.wide {
            max-width: 300px;
            min-width: 150px;
        }

        /* Manejo de contenido largo */
        .cell-content {
            display: block;
            line-height: 1.3;
            word-wrap: break-word;
        }

        .cell-content.truncated {
            max-height: 60px;
            overflow: hidden;
            position: relative;
        }

        .cell-content.truncated::after {
            content: "...";
            position: absolute;
            bottom: 0;
            right: 0;
            background: inherit;
            padding-left: 10px;
        }

        .empty-state {
            text-align: center;
            padding: 30px 20px;
            color: #94a3b8;
            background: #f8fafc;
        }

        .empty-state::before {
            content: "📋";
            display: block;
            font-size: 24px;
            margin-bottom: 8px;
        }

        .empty-state-text {
            font-size: 11px;
            font-weight: 500;
            color: #64748b;
            font-style: italic;
        }

        /* Footer con información del reporte */
        footer {
            padding: 12px 20px;
            font-size: 8px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            text-align: right;
        }

        /* Configuración optimizada para PDF */
        @page {
            margin: 10mm;
            size: A4 landscape;
        }

        @page :first {
            margin-top: 15mm;
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

        /* Optimización para tablas muy grandes */
        @media print {
            body {
                padding: 5px;
                font-size: 9px;
            }

            header {
                padding: 12px 15px;
            }

            h2 {
                font-size: 16px;
            }

            .table-wrapper {
                padding: 10px 15px;
            }

            td, th {
                padding: 6px 6px;
                font-size: 8px;
            }

            td:first-child,
            th:first-child {
                padding-left: 8px;
            }

            td:last-child,
            th:last-child {
                padding-right: 8px;
            }

            td.narrow {
                max-width: 60px;
            }

            td.medium {
                max-width: 120px;
            }

            td.wide {
                max-width: 250px;
            }
        }

        /* Estilos para números y fechas */
        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .font-mono {
            font-family: 'Courier New', monospace;
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
                            <th>{{ substr($head, 0, 50) }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $row)
                        <tr>
                            @php
                                // Seguridad: si $row no es array, intentar convertirlo
                                if (!is_array($row)) {
                                    $decoded = json_decode($row, true);
                                    $row = is_array($decoded) ? $decoded : [];
                                }
                            @endphp
                            @foreach ($row as $cellIndex => $cell)
                                @php
                                    $display = $cell;
                                    
                                    // Decodificar JSON si es necesario
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
                                    
                                    // Convertir a string
                                    $display = (string) $display;
                                    
                                    // Limitar tamaño
                                    $maxLength = 5000;
                                    $isTruncated = strlen($display) > $maxLength;
                                    if ($isTruncated) {
                                        $display = substr($display, 0, $maxLength) . '...';
                                    }
                                    
                                    // Determinar clase de ancho según contenido
                                    $cellLength = strlen($display);
                                    $cellClass = 'medium';
                                    if ($cellLength < 30) {
                                        $cellClass = 'narrow';
                                    } elseif ($cellLength > 100) {
                                        $cellClass = 'wide';
                                    }
                                @endphp
                                <td class="{{ $cellClass }}">
                                    <div class="cell-content {{ $isTruncated ? 'truncated' : '' }}">
                                        {{ $display }}
                                    </div>
                                </td>
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

        <footer>
            Total de registros: {{ count($rows) }} | Columnas: {{ count($headings) }} | Página generada: {{ now()->format('d/m/Y H:i:s') }}
        </footer>
    </div>
</body>

</html>
