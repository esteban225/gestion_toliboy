<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class VFormsStatusSummary
 * 
 * @property int $form_id
 * @property string $form_name
 * @property string $form_version
 * @property string|null $status
 * @property int $responses_count
 *
 * @package App\Models
 */
class VFormsStatusSummary extends Model
{
	protected $table = 'v_forms_status_summary';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'form_id' => 'int',
		'responses_count' => 'int'
	];

	protected $fillable = [
		'form_id',
		'form_name',
		'form_version',
		'status',
		'responses_count'
	];
}
