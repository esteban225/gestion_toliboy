<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CurrentUserSession
 * 
 * @property int $id
 * @property int $connection_id
 * @property int $user_id
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property Carbon|null $created_at
 * @property Carbon|null $expires_at
 * 
 * @property User $user
 *
 * @package App\Models
 */
class CurrentUserSession extends Model
{
	protected $table = 'current_user_sessions';
	public $timestamps = false;

	protected $casts = [
		'connection_id' => 'int',
		'user_id' => 'int',
		'expires_at' => 'datetime'
	];

	protected $fillable = [
		'connection_id',
		'user_id',
		'ip_address',
		'user_agent',
		'expires_at'
	];

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
