<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class ViewsController extends Controller
{
    /**
     * Lista todas las vistas del esquema de la BD actual.
     */
    public function index(): JsonResponse
    {
        $db = DB::getDatabaseName();

        $views = DB::table('information_schema.views')
            ->select('table_name', 'view_definition')
            ->where('table_schema', $db)
            ->orderBy('table_name')
            ->get();

        return response()->json(['views' => $views], 200);
    }

    /**
     * Devuelve contenido de varias vistas para un dashboard.
     */
    public function dashboard(Request $request): JsonResponse
    {
        $db = DB::getDatabaseName();

        $requested = $request->query('views');
        if ($requested) {
            $names = array_filter(array_map('trim', explode(',', $requested)));
        } else {
            $names = [
                //Usuarios/Roles
                'v_users_by_role',
                //Productos
                'v_products_by_category',
                //LOTES (Batches)
                'v_batches_by_status',
                'v_batches_by_product',
                'v_batches_lead_times',
                'v_batches_defect_rate',
                'v_inventory_monthly_summary',
                //FORMULARIOS Y RESPUESTAS
                'v_forms_status_summary',
                'v_forms_completion_rate',
                'v_form_field_usage',
                'v_form_review_time_hours',
                //JORNADAS DE TRABAJO
                'v_user_work_hours_by_month',
                'user_work_summary',
                //AUDITORÍA
                'v_audit_activity_by_table',
                'v_audit_activity_by_user_day',
                'active_sessions',
                //FORMS
                'form_response_details',
                //INVENTARIO
                'v_current_stock',
            ];
        }

        $perView = max(1, min(1000, (int) $request->query('per_view', 100)));

        // seleccionar con alias para evitar problemas de mayúsculas
        $existing = DB::table('information_schema.views')
            ->selectRaw('TABLE_NAME as table_name')
            ->where('TABLE_SCHEMA', $db)
            ->whereIn('TABLE_NAME', $names)
            ->pluck('table_name')
            ->all();

        $result = [];
        foreach ($existing as $view) {
            try {
                $query = DB::table($view);
                $total = $query->count();
                $data = $query->limit($perView)->get();

                $result[$view] = [
                    'total' => $total,
                    'per_view' => $perView,
                    'rows' => $data,
                ];
            } catch (\Throwable $e) {
                $result[$view] = ['error' => $e->getMessage()];
            }
        }

        $missing = array_values(array_diff($names, $existing));
        if (!empty($missing)) {
            $result['_missing_views'] = $missing;
        }

        return response()->json($result, 200);
    }

    /**
     * Devuelve contenido paginado de una vista concreta.
     */
    public function show(Request $request, string $view): JsonResponse
    {
        $db = DB::getDatabaseName();

        $exists = DB::table('information_schema.views')
            ->where('TABLE_SCHEMA', $db)
            ->where('TABLE_NAME', $view)
            ->exists();

        if (! $exists) {
            return response()->json(['message' => 'View not found'], 404);
        }

        $perPage = max(1, min(500, (int) $request->query('per_page', 50)));
        $page = max(1, (int) $request->query('page', 1));

        $query = DB::table($view);
        $total = $query->count();
        $data = $query->forPage($page, $perPage)->get();

        return response()->json([
            'view' => $view,
            'page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'data' => $data,
        ], 200);
    }

    /**
     * Endpoint para reportes por nombre (wrapper hacia ReportsController logic).
     * GET /api/reports/{reportName}
     */
    public function report(Request $request, string $reportName)
    {
        // reusar ReportsController para mantener lógica centralizada
        return app(\App\Http\Controllers\ReportsController::class)->report($request, $reportName);
    }
}
