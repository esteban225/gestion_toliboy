<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Reporte de formulario' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            font-size: 12px; /* Aumentado para A0 */
            color: #1a202c;
            line-height: 1.5;
            background: #f8fafc;
            padding: 0;
        }

        .container {
            max-width: 100%;
            background: #ffffff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        header {
            background: #2563eb;
            padding: 20px 24px;
            color: #ffffff;
            border-bottom: 4px solid #1e40af;
        }

        h2 {
            font-size: 24px; /* Optimizado para A2 */
            font-weight: 700;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
            word-break: break-word;
            overflow-wrap: break-word;
        }

        .meta {
            font-size: 11px;
            color: #ffffff;
            font-weight: 500;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 8px;
            opacity: 0.95;
        }

        .meta::before {
            content: "📅";
            font-size: 14px;
        }

        .table-wrapper {
            padding: 20px 24px 24px;
            overflow-x: auto;
            overflow-y: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #cbd5e1;
            table-layout: auto;
            min-width: 800px;
        }

        thead {
            background: #e2e8f0;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        thead th {
            color: #0f172a;
            font-weight: 700;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 14px 12px;
            text-align: left;
            border-bottom: 2px solid #64748b;
            border-right: 1px solid #cbd5e1;
            word-break: break-word;
            overflow-wrap: break-word;
            white-space: normal;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
        }

        thead th:last-child {
            border-right: none;
        }

        tbody tr {
            border-bottom: 1px solid #e2e8f0;
            transition: background-color 0.2s ease;
        }

        tbody tr:hover {
            background-color: #f1f5f9 !important;
        }

        tbody tr:last-child {
            border-bottom: none;
        }

        tbody tr:nth-child(even) {
            background: #f1f5f9;
        }

        tbody tr:nth-child(odd) {
            background: #ffffff;
        }

        td {
            padding: 12px 12px;
            color: #1e293b;
            font-size: 10px;
            vertical-align: top;
            border-right: 1px solid #e2e8f0;
            word-break: break-word;
            overflow-wrap: break-word;
            white-space: normal;
        }

        td:last-child {
            border-right: none;
        }

        td:first-child,
        th:first-child {
            padding-left: 16px;
        }

        td:last-child,
        th:last-child {
            padding-right: 16px;
        }

        /* Sistema de anchos mejorado y más flexible */
        td.narrow {
            width: 8%;
            min-width: 80px;
            max-width: 120px;
        }

        td.medium {
            width: 15%;
            min-width: 150px;
            max-width: 250px;
        }

        td.wide {
            width: 25%;
            min-width: 200px;
            max-width: 400px;
        }

        td.auto {
            width: auto;
        }

        /* Manejo de contenido largo mejorado */
        .cell-content {
            display: block;
            line-height: 1.4;
            word-wrap: break-word;
            max-height: none;
        }

        .cell-content.truncated {
            max-height: 80px;
            overflow: hidden;
            position: relative;
            padding-bottom: 4px;
        }

        .cell-content.truncated::after {
            content: "...";
            position: absolute;
            bottom: 0;
            right: 0;
            background: linear-gradient(to right, transparent, inherit 20%);
            padding-left: 20px;
            padding-right: 4px;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #64748b;
            background: #f8fafc;
        }

        .empty-state::before {
            content: "📋";
            display: block;
            font-size: 32px;
            margin-bottom: 12px;
            opacity: 0.7;
        }

        .empty-state-text {
            font-size: 13px;
            font-weight: 500;
            color: #475569;
            font-style: italic;
        }

        /* Footer mejorado */
        footer {
            padding: 16px 24px;
            font-size: 9px;
            color: #64748b;
            border-top: 2px solid #cbd5e1;
            background: #f8fafc;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
        }

        .footer-stats {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
        }

        .footer-stat {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .footer-stat strong {
            color: #0f172a;
            font-weight: 600;
        }

        /* Configuración optimizada para PDF - Tamaño A2 */
        @page {
            margin: 10mm;
            size: 594mm 420mm; /* A2 landscape - 594mm x 420mm */
        }

        @page :first {
            margin-top: 12mm;
        }

        /* Evitar cortes de página */
        tbody tr {
            page-break-inside: avoid;
        }

        thead {
            display: table-header-group;
        }

        tfoot {
            display: table-footer-group;
        }

        /* Media queries para responsividad en pantalla */
        @media screen and (max-width: 1400px) {
            body {
                font-size: 10px;
            }

            h2 {
                font-size: 20px;
            }

            td, th {
                padding: 10px 10px;
                font-size: 9px;
            }
        }

        @media screen and (max-width: 1024px) {
            body {
                font-size: 9px;
            }

            h2 {
                font-size: 18px;
            }

            header {
                padding: 16px 20px;
            }

            .table-wrapper {
                padding: 16px 20px;
            }

            td, th {
                padding: 8px 8px;
                font-size: 8px;
            }

            td.narrow {
                min-width: 60px;
            }

            td.medium {
                min-width: 120px;
            }

            td.wide {
                min-width: 160px;
            }
        }

        @media screen and (max-width: 768px) {
            .meta {
                flex-direction: column;
                align-items: flex-start;
                gap: 4px;
            }

            footer {
                flex-direction: column;
                align-items: flex-start;
            }

            .footer-stats {
                width: 100%;
            }
        }

        /* Optimización para impresión en A2 */
        @media print {
            body {
                background: #ffffff;
                padding: 0;
                font-size: 11px; /* Fuente optimizada para A2 */
            }

            .container {
                box-shadow: none;
            }

            header {
                padding: 20px 28px;
                background: #2563eb;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            h2 {
                font-size: 26px; /* Header para A2 */
                color: #ffffff;
            }

            .meta {
                font-size: 12px;
                color: #ffffff;
            }

            .table-wrapper {
                padding: 20px 28px;
            }

            table {
                min-width: 100%;
            }

            thead th {
                padding: 14px 12px;
                font-size: 11px;
                background: #e2e8f0;
                color: #0f172a;
                border-bottom: 2px solid #64748b;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            td, th {
                padding: 11px 12px;
                font-size: 10px;
                color: #1e293b;
                border-right: 1px solid #cbd5e1;
                border-bottom: 1px solid #e2e8f0;
            }

            td:first-child,
            th:first-child {
                padding-left: 16px;
            }

            td:last-child,
            th:last-child {
                padding-right: 16px;
            }

            tbody tr:nth-child(even) {
                background: #f1f5f9 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            tbody tr:nth-child(odd) {
                background: #ffffff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            tbody tr:hover {
                background-color: inherit !important;
            }

            /* Anchos optimizados para A2 */
            td.narrow {
                min-width: 100px;
                max-width: 160px;
            }

            td.medium {
                min-width: 200px;
                max-width: 320px;
            }

            td.wide {
                min-width: 320px;
                max-width: 500px;
            }

            footer {
                padding: 18px 28px;
                font-size: 9px;
                background: #f1f5f9;
                border-top: 2px solid #94a3b8;
                color: #475569;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .footer-stat strong {
                font-size: 11px;
                color: #0f172a;
            }
        }

        /* Estilos para diferentes tipos de datos */
        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .font-mono {
            font-family: 'Courier New', monospace;
            font-size: 0.95em;
        }

        /* Indicadores visuales */
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 8px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .badge-success {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-warning {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-error {
            background: #fee2e2;
            color: #991b1b;
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
                            <th>{{ substr($head, 0, 60) }}</th>
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
                                    
                                    // Limitar tamaño (aumentado el límite)
                                    $maxLength = 8000;
                                    $isTruncated = strlen($display) > $maxLength;
                                    if ($isTruncated) {
                                        $display = substr($display, 0, $maxLength) . '...';
                                    }
                                    
                                    // Sistema de clasificación de ancho mejorado
                                    $cellLength = strlen($display);
                                    $cellClass = 'medium';
                                    if ($cellLength < 20) {
                                        $cellClass = 'narrow';
                                    } elseif ($cellLength > 150) {
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
                                <div class="empty-state-text">No hay datos disponibles para mostrar</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <footer>
            <div class="footer-stats">
                <div class="footer-stat">
                    <span>📊 Total de registros:</span>
                    <strong>{{ count($rows) }}</strong>
                </div>
                <div class="footer-stat">
                    <span>📋 Columnas:</span>
                    <strong>{{ count($headings) }}</strong>
                </div>
            </div>
            <div class="footer-stat">
                <span>🕐 Generado:</span>
                <strong>{{ now()->format('d/m/Y H:i:s') }}</strong>
            </div>
        </footer>
    </div>
</body>

</html>