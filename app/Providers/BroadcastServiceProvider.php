<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Registra las rutas necesarias para el broadcasting
        Broadcast::routes();

        // Carga las definiciones de canales privados
        require_once base_path('routes/channels.php');
    }
}
