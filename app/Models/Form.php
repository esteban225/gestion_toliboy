<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Form
 * 
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string|null $description
 * @property string $version
 * @property int|null $created_by
 * @property bool $is_active
 * @property int $display_order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property User|null $user
 * @property Collection|FormField[] $form_fields
 * @property Collection|FormResponse[] $form_responses
 *
 * @package App\Models
 */
class Form extends Model
{
	protected $table = 'forms';

	protected $casts = [
		'created_by' => 'int',
		'is_active' => 'bool',
		'display_order' => 'int'
	];

	protected $fillable = [
		'name',
		'code',
		'description',
		'version',
		'created_by',
		'is_active',
		'display_order'
	];

	public function user()
	{
		return $this->belongsTo(User::class, 'created_by');
	}

	public function form_fields()
	{
		return $this->hasMany(FormField::class);
	}

	public function form_responses()
	{
		return $this->hasMany(FormResponse::class);
	}
}
