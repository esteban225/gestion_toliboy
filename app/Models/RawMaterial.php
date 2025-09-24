<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RawMaterial
 *
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string|null $description
 * @property string $unit_of_measure
 * @property float $stock
 * @property float|null $min_stock
 * @property bool $is_active
 * @property int|null $created_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property User|null $user
 * @property Collection|InventoryMovement[] $inventory_movements
 */
class RawMaterial extends Model
{
    protected $table = 'raw_materials';

    protected $casts = [
        'stock' => 'float',
        'min_stock' => 'float',
        'is_active' => 'bool',
        'created_by' => 'int',
    ];

    protected $fillable = [
        'name',
        'code',
        'description',
        'unit_of_measure',
        'stock',
        'min_stock',
        'is_active',
        'created_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function inventory_movements()
    {
        return $this->hasMany(InventoryMovement::class);
    }
}
