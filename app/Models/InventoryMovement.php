<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class InventoryMovement
 * 
 * @property int $id
 * @property int $raw_material_id
 * @property int|null $batch_id
 * @property string $movement_type
 * @property float $quantity
 * @property float|null $unit_cost
 * @property string|null $notes
 * @property int|null $created_by
 * @property Carbon|null $created_at
 * 
 * @property Batch|null $batch
 * @property RawMaterial $raw_material
 * @property User|null $user
 *
 * @package App\Models
 */
class InventoryMovement extends Model
{
	protected $table = 'inventory_movements';
	public $timestamps = false;

	protected $casts = [
		'raw_material_id' => 'int',
		'batch_id' => 'int',
		'quantity' => 'float',
		'unit_cost' => 'float',
		'created_by' => 'int'
	];

	protected $fillable = [
		'raw_material_id',
		'batch_id',
		'movement_type',
		'quantity',
		'unit_cost',
		'notes',
		'created_by'
	];

	public function batch()
	{
		return $this->belongsTo(Batch::class);
	}

	public function raw_material()
	{
		return $this->belongsTo(RawMaterial::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'created_by');
	}
}
