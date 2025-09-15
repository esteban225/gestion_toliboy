<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class VBatchesDefectRate
 * 
 * @property int $batch_id
 * @property string $code
 * @property string|null $product_name
 * @property int $quantity
 * @property int $defect_quantity
 * @property float|null $defect_rate_pct
 *
 * @package App\Models
 */
class VBatchesDefectRate extends Model
{
	protected $table = 'v_batches_defect_rate';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'batch_id' => 'int',
		'quantity' => 'int',
		'defect_quantity' => 'int',
		'defect_rate_pct' => 'float'
	];

	protected $fillable = [
		'batch_id',
		'code',
		'product_name',
		'quantity',
		'defect_quantity',
		'defect_rate_pct'
	];
}
