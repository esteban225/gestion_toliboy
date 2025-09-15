<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class VProductsByCategory
 * 
 * @property string $category
 * @property bool $is_active
 * @property int $product_count
 * @property float|null $avg_price
 * @property float|null $min_price
 * @property float|null $max_price
 *
 * @package App\Models
 */
class VProductsByCategory extends Model
{
	protected $table = 'v_products_by_category';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'is_active' => 'bool',
		'product_count' => 'int',
		'avg_price' => 'float',
		'min_price' => 'float',
		'max_price' => 'float'
	];

	protected $fillable = [
		'category',
		'is_active',
		'product_count',
		'avg_price',
		'min_price',
		'max_price'
	];
}
