<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UserWorkSummary
 * 
 * @property int $user_id
 * @property string $user_name
 * @property int $total_work_days
 * @property float|null $total_hours
 * @property float|null $total_overtime
 * @property Carbon|null $first_work_date
 * @property Carbon|null $last_work_date
 *
 * @package App\Models
 */
class UserWorkSummary extends Model
{
	protected $table = 'user_work_summary';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'user_id' => 'int',
		'total_work_days' => 'int',
		'total_hours' => 'float',
		'total_overtime' => 'float',
		'first_work_date' => 'datetime',
		'last_work_date' => 'datetime'
	];

	protected $fillable = [
		'user_id',
		'user_name',
		'total_work_days',
		'total_hours',
		'total_overtime',
		'first_work_date',
		'last_work_date'
	];
}
