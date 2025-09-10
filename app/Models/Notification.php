<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Notification
 * 
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string $message
 * @property string $type
 * @property bool $is_read
 * @property string|null $related_table
 * @property int|null $related_id
 * @property Carbon|null $expires_at
 * @property Carbon|null $created_at
 * 
 * @property User $user
 *
 * @package App\Models
 */
class Notification extends Model
{
	protected $table = 'notifications';
	public $timestamps = false;

	protected $casts = [
		'user_id' => 'int',
		'is_read' => 'bool',
		'related_id' => 'int',
		'expires_at' => 'datetime'
	];

	protected $fillable = [
		'user_id',
		'title',
		'message',
		'type',
		'is_read',
		'related_table',
		'related_id',
		'expires_at'
	];

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
