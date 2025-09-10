<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class FormField
 * 
 * @property int $id
 * @property int $form_id
 * @property string $label
 * @property string $field_code
 * @property string $type
 * @property bool $required
 * @property array|null $options
 * @property array|null $validation_rules
 * @property int $field_order
 * @property bool $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Form $form
 * @property Collection|FormResponseValue[] $form_response_values
 *
 * @package App\Models
 */
class FormField extends Model
{
	protected $table = 'form_fields';

	protected $casts = [
		'form_id' => 'int',
		'required' => 'bool',
		'options' => 'json',
		'validation_rules' => 'json',
		'field_order' => 'int',
		'is_active' => 'bool'
	];

	protected $fillable = [
		'form_id',
		'label',
		'field_code',
		'type',
		'required',
		'options',
		'validation_rules',
		'field_order',
		'is_active'
	];

	public function form()
	{
		return $this->belongsTo(Form::class);
	}

	public function form_response_values()
	{
		return $this->hasMany(FormResponseValue::class, 'field_id');
	}
}
