<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class VCurrentStock
 *
 * @property int $raw_material_id
 * @property string $name
 * @property string $code
 * @property string $unit_of_measure
 * @property float $recorded_stock
 * @property float $movements_stock
 * @property float $current_stock
 * @property float|null $min_stock
 */
class VCurrentStock extends Model
{
    protected $table = 'v_current_stock';

    public $incrementing = false;

    public $timestamps = false;

    protected $casts = [
        'raw_material_id' => 'int',
        'recorded_stock' => 'float',
        'movements_stock' => 'float',
        'current_stock' => 'float',
        'min_stock' => 'float',
    ];

    protected $fillable = [
        'raw_material_id',
        'name',
        'code',
        'unit_of_measure',
        'recorded_stock',
        'movements_stock',
        'current_stock',
        'min_stock',
    ];
}
