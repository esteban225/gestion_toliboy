<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Aquí defines los canales de broadcasting que tu aplicación soporta.
| Laravel los usará para autorizar el acceso a canales privados.
|
*/

Broadcast::channel('notifications.global', fn () => true);

Broadcast::channel('notifications.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});
