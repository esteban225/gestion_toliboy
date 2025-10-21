<?php

namespace App\Modules\Forms\Infrastructure\Services;

use App\Modules\Notifications\Domain\Entities\NotificationEntity;
use App\Modules\Notifications\Domain\Services\NotificationService;
use Illuminate\Support\Facades\Auth;

/**
 * Caso de uso para notificar cuando se añade un movimiento de inventario.
 *
 * Este caso de uso encapsula la lógica de creación de una notificación grupal
 * dirigida a los usuarios con el rol 'DEV' cuando se registra un nuevo movimiento de inventario.
 *
 * Utiliza el NotificationService para delegar la creación y asociación de la notificación.
 *
 * Parámetros esperados en $data:
 * - movement_id: (int|null) ID del movimiento de inventario relacionado
 *
 * Ejemplo de uso:
 *   $useCase = new InvNotificationUseCase($notificationService);
 *   $useCase->execute(['movement_id' => 123]);
 */
class FormNotifyService
{
    private NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Ejecuta la creación de la notificación grupal para movimientos de inventario.
     *
     * @param   mixed  $form  Nombre o datos del formulario completado
     * @return void
     */
    public function execute($form)
    {
        $dataUser = Auth::user();

        $extra = [
            'role' => 'DEV',
        ];

        // Construye la entidad de notificación con los datos relevantes
            $notification = NotificationEntity::fromArray([
                null,
                'title' => 'Formulario completado: ' . $form,
                'message' => 'El usuario ' . $dataUser->name . ' ha completado el formulario.',
                'type' => 'info',
                'scope' => 'group',
                'is_read' => false,
                'related_table' => 'form_responses',
                'related_id' => null,
                'user_id' => null,
            ]);

        // Crea la notificación y la asocia a los usuarios del rol indicado
        $this->notificationService->createNotification($notification, $extra);
    }
}
