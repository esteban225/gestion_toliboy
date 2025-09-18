<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>{{ $title ?? 'Reporte de formulario' }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size:12px; color:#222; }
        header { margin-bottom:12px; }
        table { width:100%; border-collapse:collapse; }
        th, td { border:1px solid #ddd; padding:6px; text-align:left; vertical-align:top; }
        thead th { background:#2c3e50; color:#fff; }
        tbody tr:nth-child(even) { background:#f7f7f7; }
        .meta { font-size:11px; color:#666; margin-bottom:8px; }
    </style>
</head>
<body>
    <header>
        <h2 style="margin:0 0 4px 0">{{ $title ?? 'Reporte de formulario' }}</h2>
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
                    <td colspan="{{ max(1, count($headings)) }}">No hay datos</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
