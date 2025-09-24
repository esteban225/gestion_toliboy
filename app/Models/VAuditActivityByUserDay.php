<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class VAuditActivityByUserDay
 *
 * @property Carbon|null $activity_date
 * @property int|null $user_id
 * @property string|null $user_name
 * @property string $action
 * @property int $events_count
 */
class VAuditActivityByUserDay extends Model
{
    protected $table = 'v_audit_activity_by_user_day';

    public $incrementing = false;

    public $timestamps = false;

    protected $casts = [
        'activity_date' => 'datetime',
        'user_id' => 'int',
        'events_count' => 'int',
    ];

    protected $fillable = [
        'activity_date',
        'user_id',
        'user_name',
        'action',
        'events_count',
    ];
}
