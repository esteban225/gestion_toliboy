<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class VNotificationsSummaryByUser
 * 
 * @property int $user_id
 * @property string $user_name
 * @property string|null $type
 * @property bool|null $is_read
 * @property int $notifications_count
 *
 * @package App\Models
 */
class VNotificationsSummaryByUser extends Model
{
	protected $table = 'v_notifications_summary_by_user';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'user_id' => 'int',
		'is_read' => 'bool',
		'notifications_count' => 'int'
	];

	protected $fillable = [
		'user_id',
		'user_name',
		'type',
		'is_read',
		'notifications_count'
	];
}
