<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class VBatchesByProduct
 *
 * @property int $product_id
 * @property string $product_name
 * @property string|null $status
 * @property int $batch_count
 * @property float|null $total_quantity
 * @property float|null $total_defects
 */
class VBatchesByProduct extends Model
{
    protected $table = 'v_batches_by_product';

    public $incrementing = false;

    public $timestamps = false;

    protected $casts = [
        'product_id' => 'int',
        'batch_count' => 'int',
        'total_quantity' => 'float',
        'total_defects' => 'float',
    ];

    protected $fillable = [
        'product_id',
        'product_name',
        'status',
        'batch_count',
        'total_quantity',
        'total_defects',
    ];
}
