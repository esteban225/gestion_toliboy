<?php

namespace App\Modules\Forms\Infrastructure\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FormsReportRepository
{
    public function fetchFormReport(int $formId, array $filters = [], int $limit = 1000): array
    {
        // 1. Obtener campos del formulario (ordenados)
        $fields = DB::table('form_fields')
            ->where('form_id', $formId)
            ->orderBy('id')
            ->get();

        // --- INICIO DE LA DEPURACIÓN ---
        // Si no se encuentran campos, el reporte estará vacío. Registramos esto.
        if ($fields->isEmpty()) {
            Log::warning("No se encontraron campos (form_fields) para el form_id: {$formId}. El reporte de valores estará vacío.");

            return [
                'headings' => [],
                'rows' => [],
            ];
        }
        // --- FIN DE LA DEPURACIÓN ---

        $fieldIds = $fields->pluck('id')->all();
        Log::info("Campos encontrados para form_id {$formId}", ['field_ids' => $fieldIds]);

        // 2. Obtener respuestas
        $queryResponses = DB::table('form_responses as fr')
            ->select('fr.id', 'fr.user_id', 'fr.created_at', 'fr.status')
            ->where('fr.form_id', $formId);

        if (! empty($filters['date_from'])) {
            $queryResponses->where('fr.created_at', '>=', $filters['date_from']);
        }
        if (! empty($filters['date_to'])) {
            $queryResponses->where('fr.created_at', '<=', $filters['date_to']);
        }

        $responses = $queryResponses->limit($limit)->get();
        Log::info("Respuestas encontradas para form_id {$formId}", ['responses' => $responses->pluck('id')->all()]);

        if ($responses->isEmpty()) {
            return [
                'headings' => $fields->pluck('label')->all(),
                'rows' => [],
            ];
        }

        // 3. Cargar todos los valores para esas respuestas
        $responseIds = $responses->pluck('id')->all();

        // Usamos los nombres de columna que confirmaste en Tinker
        $values = DB::table('form_response_values')
            ->whereIn('response_id', $responseIds)
            ->whereIn('field_id', $fieldIds)
            ->get();
        Log::info('Valores crudos encontrados', ['values_count' => $values->count()]);

        // 4. Organizar valores en un mapa para fácil acceso
        $map = [];
        foreach ($values as $v) {
            $val = $v->value ?? $v->file_path ?? '';
            $map[$v->response_id][$v->field_id] = $val;
        }
        Log::info('Mapa de valores procesado', ['map_keys' => array_keys($map)]);

        // 5. Construir las filas del reporte
        $headings = $fields->pluck('label')->all();
        $rows = [];
        foreach ($responses as $resp) {
            $row = [];
            foreach ($fields as $field) {
                // Usamos el mapa para encontrar el valor correspondiente, o dejamos en blanco si no existe
                $value = $map[$resp->id][$field->id] ?? '';
                
                // Garantizar que el valor es un array de valores (no una row que sea string)
                // Si es un string JSON, decodificarlo
                if (is_string($value)) {
                    $decoded = json_decode($value, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $value = $decoded;
                    }
                }
                
                // Convertir arrays a string legible para la celda
                if (is_array($value)) {
                    $value = implode(', ', array_map(fn($v) => (string) $v, $value));
                }
                
                $row[] = $value;
            }
            // CRÍTICO: Asegurar que $row es SIEMPRE un array
            if (!is_array($row)) {
                Log::error('Row no es array en fetchFormReport', ['row' => $row, 'type' => gettype($row)]);
                $row = [];
            }
            $rows[] = $row;
        }

        return [
            'headings' => $headings,
            'rows' => $rows,
        ];
    }
}
