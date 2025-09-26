<?php

namespace App\Jobs;

use App\Models\RawMaterial;
use App\Modules\Notifications\Domain\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckLowStockJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $materials = RawMaterial::all();

        foreach ($materials as $material) {
            if ($material->min_stock !== null && $material->stock <= $material->min_stock) {
                app(NotificationService::class)->notify([
                    'user_id' => null,
                    'title' => "Stock bajo: {$material->name}",
                    'message' => "El artículo {$material->name} tiene stock bajo ({$material->stock} unidades).",
                    'type' => 'warning',
                    'related_table' => 'raw_materials',
                    'related_id' => $material->id,
                ]);
            }
        }
    }
}
