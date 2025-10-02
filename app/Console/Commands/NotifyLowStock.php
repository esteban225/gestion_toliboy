<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class NotifyLowStock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:notify-low-stock {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notifica sobre el stock bajo de productos en la fecha indicada (por defecto hoy)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando notificación de stock bajo...(sin implementar)');
    }
}
