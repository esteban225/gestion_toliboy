<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class VAuditActivityByTable
 * 
 * @property string $table_name
 * @property string $action
 * @property int $total_count
 * @property float|null $last_30d_count
 * @property Carbon|null $last_activity_at
 *
 * @package App\Models
 */
class VAuditActivityByTable extends Model
{
	protected $table = 'v_audit_activity_by_table';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'total_count' => 'int',
		'last_30d_count' => 'float',
		'last_activity_at' => 'datetime'
	];

	protected $fillable = [
		'table_name',
		'action',
		'total_count',
		'last_30d_count',
		'last_activity_at'
	];
}
