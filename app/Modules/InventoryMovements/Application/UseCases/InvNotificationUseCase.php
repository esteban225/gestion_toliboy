<?php

namespace App\Modules\InventoryMovements\Application\UseCases;

use App\Modules\Notifications\Domain\Entities\NotificationEntity;
use App\Modules\Notifications\Domain\Services\NotificationService;

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
class InvNotificationUseCase
{
    private NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Ejecuta la creación de la notificación grupal para movimientos de inventario.
     *
     * @param  array  $data  Datos relevantes para la notificación (debe incluir 'movement_id')
     * @return void
     */
    public function execute(array $data)
    {
        $extra = [
            'role' => 'DEV',
        ];
        // Construye la entidad de notificación con los datos relevantes
        $notification = NotificationEntity::fromArray([
            null,
            'title' => 'Añadieron un movimiento de inventario',
            'message' => '',
            'type' => 'info',
            'scope' => 'group',
            'is_read' => false,
            'related_table' => 'inventory_movements',
            'related_id' => $data['movement_id'] ?? null,
        ]);

        // Crea la notificación y la asocia a los usuarios del rol indicado
        $this->notificationService->createNotification($notification, $extra);
    }
}
