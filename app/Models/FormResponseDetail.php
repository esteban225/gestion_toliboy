<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class FormResponseDetail
 * 
 * @property int $response_id
 * @property string $form_name
 * @property string $form_version
 * @property string $user_name
 * @property string|null $batch_name
 * @property string $status
 * @property Carbon|null $submitted_at
 * @property int $field_id
 * @property string $field_label
 * @property string $field_code
 * @property string $field_type
 * @property string|null $value
 * @property string|null $file_path
 * @property Carbon|null $value_created_at
 *
 * @package App\Models
 */
class FormResponseDetail extends Model
{
	protected $table = 'form_response_details';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'response_id' => 'int',
		'submitted_at' => 'datetime',
		'field_id' => 'int',
		'value_created_at' => 'datetime'
	];

	protected $fillable = [
		'response_id',
		'form_name',
		'form_version',
		'user_name',
		'batch_name',
		'status',
		'submitted_at',
		'field_id',
		'field_label',
		'field_code',
		'field_type',
		'value',
		'file_path',
		'value_created_at'
	];
}
