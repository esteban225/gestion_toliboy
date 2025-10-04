<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Forms\Http\Controllers\{
    FormsController,
    FormFieldController,
    FormReportsController
};
use App\Modules\Forms\Http\Controllers\FormResponseController;
use App\Http\Middleware\SetDbSessionUser;

/**
 * Grupo principal para rutas de gestión de formularios.
 * Middleware: Autenticación JWT + Roles permitidos.
 */
Route::middleware(['api', 'jwt.auth', 'role:DEV,GG,INGPL,INGPR', SetDbSessionUser::class])
    ->prefix('forms')
    ->group(function () {

        // 📄 CRUD principal de formularios
        Route::apiResource('/', FormsController::class)->parameters(['' => 'form']);

        // 🧩 CRUD de campos por formulario
        Route::apiResource('/{form}/fields', FormFieldController::class);

        // 📋 Respuestas de formularios
        Route::apiResource('/responses', FormResponseController::class);

        // 🔎 Revisión de respuesta específica
        Route::post('/responses/{id}/review', [FormResponseController::class, 'review']);

        // 🧾 Reglas de validación dinámicas
        Route::get('/{formId}/validation-rules', [FormResponseController::class, 'getValidationRules']);
    });

/**
 * 📊 Reportes PDF de formularios
 */
Route::get('forms/{formId}/report/pdf', [FormReportsController::class, 'pdf']);
