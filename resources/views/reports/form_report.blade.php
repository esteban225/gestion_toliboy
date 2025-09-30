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
            font-family: 'Segoe UI', 'DejaVu Sans', Tahoma, sans-serif;
            font-size: 11px;
            color: #2c3e50;
            line-height: 1.6;
            background: #ffffff;
            padding: 20px;
        }

        header {
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 3px solid #3498db;
        }

        h2 {
            font-size: 22px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .meta {
            font-size: 10px;
            color: #7f8c8d;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .meta::before {
            content: "📅";
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            border-radius: 8px;
            overflow: hidden;
        }

        thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        thead th {
            background: transparent;
            color: #ffffff;
            font-weight: 600;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 12px 10px;
            text-align: left;
            border: none;
        }

        tbody tr {
            transition: background-color 0.2s ease;
        }

        tbody tr:nth-child(odd) {
            background: #ffffff;
        }

        tbody tr:nth-child(even) {
            background: #f8f9fa;
        }

        tbody tr:hover {
            background: #e8f4f8;
        }

        td {
            padding: 10px;
            border: none;
            border-bottom: 1px solid #e9ecef;
            vertical-align: top;
            color: #495057;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        td:first-child,
        th:first-child {
            padding-left: 16px;
        }

        td:last-child,
        th:last-child {
            padding-right: 16px;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #95a5a6;
            font-style: italic;
            background: #f8f9fa;
        }

        .empty-state::before {
            content: "📋";
            display: block;
            font-size: 32px;
            margin-bottom: 12px;
        }

        @media print {
            body {
                padding: 10px;
            }

            table {
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <header>
        <h2>{{ $title ?? 'Reporte de formulario' }}</h2>
        <div class="meta">
            Generado: {{ $generated_at ?? now() }}
        </div>
    </header>

    <table>
        <thead>
            <tr>
                @foreach($headings as $head)
                    <th>{{ $head }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
                <tr>
                    @foreach($row as $cell)
                        @php
                            $display = $cell;
                            if (is_string($cell)) {
                                $decoded = json_decode($cell, true);
                                if (json_last_error() === JSON_ERROR_NONE) {
                                    if (is_array($decoded)) {
                                        $display = implode(', ', array_map(fn($v) => (string)$v, $decoded));
                                    } else {
                                        $display = (string)$decoded;
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
                        No hay datos disponibles
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
