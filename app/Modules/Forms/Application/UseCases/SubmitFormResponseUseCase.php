<?php

namespace App\Modules\Forms\Application\UseCases;

use App\Modules\Forms\Domain\Repository\FormResponseRepositoryI;
use App\Modules\Forms\Domain\Services\FormFieldValidatorService;
use App\Modules\Forms\Domain\Entities\FormResponseEntity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Form;
use Exception;

class SubmitFormResponseUseCase
{
    public function __construct(
        private FormResponseRepositoryI $repository,
        private FormFieldValidatorService $validator
    ) {}

    /**
     * Ejecuta el proceso de envío de respuesta de formulario
     *
     * @param array $data Datos validados del formulario
     * @param int $userId ID del usuario autenticado
     * @return array Resultado con status, mensaje y datos
     */
    public function execute(array $data): array
    {
        try {
            $form = Form::with('form_fields')->findOrFail($data['form_id']);
            if (! $form->is_active) {
                return [
                    'status' => false,
                    'message' => 'El formulario no está activo',
                    'data' => null,
                ];
            }
            $userId = Auth::id(); // Obtener el ID del usuario autenticado
            $this->validator->validate($form, $data['values']);
            DB::beginTransaction();

            try {
                $entity = new FormResponseEntity(
                    null,
                    $data['form_id'],
                    $userId,
                    $data['batch_id'] ?? null,
                    $data['status'] ?? 'in_progress',
                    ($data['status'] ?? 'in_progress') === 'completed' ? now()->toDateTimeString() : null,
                    null,
                    null,
                    null
                );
                $response = $this->repository->createWithValues($entity, $data['values']);
                DB::commit();
                return [
                    'status' => true,
                    'message' => 'Respuesta de formulario enviada exitosamente',
                    'data' => $response->toArray(),
                ];
            } catch (Exception $e) {
                DB::rollBack();
                return [
                    'status' => false,
                    'message' => 'Error al enviar la respuesta del formulario',
                    'data' => $e->getMessage(),
                ];
            }
        } catch (Exception $e) {
            return [
                'status' => false,
                'message' => 'Ocurrió un error al procesar la solicitud.',
                'data' => $e->getMessage(),
            ];
        }
    }
}
