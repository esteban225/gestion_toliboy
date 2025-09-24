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
 * @property int|null $user_id
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property string $payload
 * @property int $last_activity
 * @property int $connection_id
 * @property Carbon|null $created_at
 * @property Carbon|null $expires_at
 * @property User|null $user
 */
class CurrentUserSession extends Model
{
    protected $table = 'current_user_sessions';

    public $timestamps = false;

    protected $casts = [
        'user_id' => 'int',
        'last_activity' => 'int',
        'connection_id' => 'int',
        'expires_at' => 'datetime',
    ];

    protected $fillable = [
        'user_id',
        'ip_address',
        'user_agent',
        'payload',
        'last_activity',
        'connection_id',
        'expires_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
