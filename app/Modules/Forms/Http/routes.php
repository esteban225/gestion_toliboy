<?php

use App\Http\Middleware\SetDbSessionUser;
use App\Modules\Forms\Http\Controllers\FormFieldController;
use App\Modules\Forms\Http\Controllers\FormReportsController;
use App\Modules\Forms\Http\Controllers\FormResponseController;
use App\Modules\Forms\Http\Controllers\FormsController;
use Illuminate\Support\Facades\Route;

/**
 * Grupo principal para rutas de gestión de formularios.
 * Middleware: Autenticación JWT + Roles permitidos.
 */
Route::middleware(['api', 'jwt.auth', SetDbSessionUser::class])
    ->prefix('forms')
    ->group(function () {

        /**
         * 📋 RUTAS DE RESPUESTAS DE FORMULARIOS
         * Se definen antes para evitar conflicto con {form}
         */
        Route::prefix('responses')->group(function () {
            Route::get('/', [FormResponseController::class, 'index'])->name('responses.index');
            Route::post('/', [FormResponseController::class, 'store'])->name('responses.store');
            Route::get('/{response}', [FormResponseController::class, 'show'])->name('responses.show');
            Route::put('/{response}', [FormResponseController::class, 'update'])->name('responses.update');
            Route::delete('/{response}', [FormResponseController::class, 'destroy'])->name('responses.destroy');

            // 🔎 Revisión de respuesta específica
            Route::post('/{id}/review', [FormResponseController::class, 'review'])->name('responses.review');
        });

        /**
         * 🧾 Reglas de validación dinámicas
         */
        Route::get('/{formId}/validation-rules', [FormResponseController::class, 'getValidationRules'])
            ->whereNumber('formId')
            ->name('forms.validationRules');

        /**
         * 🧩 CRUD de campos por formulario
         */
        Route::apiResource('{form}/fields', FormFieldController::class)
            ->whereNumber('form');

        /**
         * 📄 CRUD principal de formularios
         */
        Route::apiResource('/', FormsController::class)
            ->parameters(['' => 'form'])
            ->whereNumber('form');
    });

/**
 * 📊 Reportes PDF de formularios
 */
Route::get('forms/{formId}/report/pdf', [FormReportsController::class, 'pdf'])
    ->whereNumber('formId')
    ->middleware(['api', 'jwt.auth', 'role:DEV,GG,INGPL,INGPR', SetDbSessionUser::class])
    ->name('forms.report.pdf');
