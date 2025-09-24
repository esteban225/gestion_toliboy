<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class VInventoryMonthlySummary
 *
 * @property int $raw_material_id
 * @property string $raw_material_name
 * @property string|null $yearmonth
 * @property string|null $month_start
 * @property float|null $in_qty
 * @property float|null $out_qty
 * @property float|null $adjustment_qty
 * @property float|null $net_qty
 * @property float|null $total_in_value
 */
class VInventoryMonthlySummary extends Model
{
    protected $table = 'v_inventory_monthly_summary';

    public $incrementing = false;

    public $timestamps = false;

    protected $casts = [
        'raw_material_id' => 'int',
        'in_qty' => 'float',
        'out_qty' => 'float',
        'adjustment_qty' => 'float',
        'net_qty' => 'float',
        'total_in_value' => 'float',
    ];

    protected $fillable = [
        'raw_material_id',
        'raw_material_name',
        'yearmonth',
        'month_start',
        'in_qty',
        'out_qty',
        'adjustment_qty',
        'net_qty',
        'total_in_value',
    ];
}
