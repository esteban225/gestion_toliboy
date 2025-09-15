<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ActiveSession
 * 
 * @property int $id
 * @property int $connection_id
 * @property int|null $user_id
 * @property string $user_name
 * @property string $email
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property Carbon|null $created_at
 * @property Carbon|null $expires_at
 * @property Carbon|null $remaining_time
 *
 * @package App\Models
 */
class ActiveSession extends Model
{
	protected $table = 'active_sessions';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id' => 'int',
		'connection_id' => 'int',
		'user_id' => 'int',
		'expires_at' => 'datetime',
		'remaining_time' => 'datetime'
	];

	protected $fillable = [
		'id',
		'connection_id',
		'user_id',
		'user_name',
		'email',
		'ip_address',
		'user_agent',
		'expires_at',
		'remaining_time'
	];
}
