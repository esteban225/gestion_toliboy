<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Product
 * 
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string|null $category
 * @property string|null $description
 * @property array|null $specifications
 * @property float|null $unit_price
 * @property bool $is_active
 * @property int|null $created_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property User|null $user
 * @property Collection|Batch[] $batches
 *
 * @package App\Models
 */
class Product extends Model
{
	protected $table = 'products';

	protected $casts = [
		'specifications' => 'json',
		'unit_price' => 'float',
		'is_active' => 'bool',
		'created_by' => 'int'
	];

	protected $fillable = [
		'name',
		'code',
		'category',
		'description',
		'specifications',
		'unit_price',
		'is_active',
		'created_by'
	];

	public function user()
	{
		return $this->belongsTo(User::class, 'created_by');
	}

	public function batches()
	{
		return $this->hasMany(Batch::class);
	}
}
