<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class VStockBelowMin
 *
 * @property int $raw_material_id
 * @property string $name
 * @property string $code
 * @property string $unit_of_measure
 * @property float $current_stock
 * @property float|null $min_stock
 * @property float|null $shortage
 */
class VStockBelowMin extends Model
{
    protected $table = 'v_stock_below_min';

    public $incrementing = false;

    public $timestamps = false;

    protected $casts = [
        'raw_material_id' => 'int',
        'current_stock' => 'float',
        'min_stock' => 'float',
        'shortage' => 'float',
    ];

    protected $fillable = [
        'raw_material_id',
        'name',
        'code',
        'unit_of_measure',
        'current_stock',
        'min_stock',
        'shortage',
    ];
}
