<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class VUserWorkHoursByMonth
 * 
 * @property int $user_id
 * @property string $user_name
 * @property string|null $yearmonth
 * @property int $days_worked
 * @property float|null $total_hours
 * @property float|null $total_overtime_hours
 *
 * @package App\Models
 */
class VUserWorkHoursByMonth extends Model
{
	protected $table = 'v_user_work_hours_by_month';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'user_id' => 'int',
		'days_worked' => 'int',
		'total_hours' => 'float',
		'total_overtime_hours' => 'float'
	];

	protected $fillable = [
		'user_id',
		'user_name',
		'yearmonth',
		'days_worked',
		'total_hours',
		'total_overtime_hours'
	];
}
