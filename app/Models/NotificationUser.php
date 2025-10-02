<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class NotificationUser
 *
 * @property int $notification_id
 * @property int $user_id
 * @property bool $is_read
 * @property Carbon|null $read_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Notification $notification
 * @property User $user
 */
class NotificationUser extends Model
{
    protected $table = 'notification_user';

    public $incrementing = false;

    protected $casts = [
        'notification_id' => 'int',
        'user_id' => 'int',
        'is_read' => 'bool',
        'read_at' => 'datetime',
    ];

    protected $fillable = [
        'is_read',
        'read_at',
    ];

    public function notification()
    {
        return $this->belongsTo(Notification::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
