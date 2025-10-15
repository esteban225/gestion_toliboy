<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Evento NotificationCreated
 *
 * Este evento se dispara cuando se crea una nueva notificación en el sistema.
 * Implementa ShouldBroadcast para emitir la notificación en tiempo real a los usuarios correspondientes.
 *
 * - Si userIds está vacío, la notificación se emite en el canal global ('notifications.global').
 * - Si hay userIds, se emite en canales privados individuales ('notifications.{userId}').
 *
 * El payload de la notificación excluye el campo 'is_read' y puede ser consumido por clientes frontend para mostrar alertas en tiempo real.
 *
 * Métodos principales:
 * - broadcastOn: Define los canales de emisión según los usuarios destinatarios.
 * - broadcastWith: Define la estructura de datos enviada en el evento.
 * - broadcastAs: Define el nombre del evento para el frontend ('notification.created').
 */
class NotificationCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notification;

    public $userIds;

    /**
     * Crea una nueva instancia del evento.
     *
     * @param  $notification  Entidad de notificación creada
     * @param  array  $userIds  IDs de usuarios destinatarios (vacío para global)
     */
    public function __construct($notification, array $userIds = [])
    {
        $this->notification = $notification;
        $this->userIds = $userIds;
    }

    /**
     * Obtiene los canales en los que se emitirá el evento.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel|string> Canales de emisión
     */
    public function broadcastOn()
    {
        if (empty($this->userIds)) {
            return ['notifications.global'];
        }

        return array_map(fn ($userId) => new Channel('notifications.'.$userId), $this->userIds);
    }

    /**
     * Define la estructura de datos que se enviará en el evento broadcast.
     *
     * @return array Datos de la notificación para el frontend
     */
    public function broadcastWith()
    {
        // Si la entidad tiene un método toArray, úsalo
        if (is_object($this->notification) && method_exists($this->notification, 'toArray')) {
            $payload = $this->notification->toArray();
        }
        // Si ya es un array, úsalo directamente
        elseif (is_array($this->notification)) {
            $payload = $this->notification;
        }
        // Si es un objeto simple, conviértelo a array genérico
        else {
            $payload = (array) $this->notification;
        }

        // Excluir campo is_read si existe
        unset($payload['is_read']);

        return [
            'id' => $payload['id'] ?? null,
            'title' => $payload['title'] ?? null,
            'message' => $payload['message'] ?? null,
            'type' => $payload['type'] ?? null,
            'scope' => $payload['scope'] ?? null,
            'related_table' => $payload['related_table'] ?? null,
            'related_id' => $payload['related_id'] ?? null,
            'user_id' => $this->userIds,
        ];
    }

    /**
     * Define el nombre del evento broadcast para el frontend.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'notification.created';
    }
}
