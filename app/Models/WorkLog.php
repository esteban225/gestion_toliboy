<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class WorkLog
 *
 * @property int $id
 * @property int $user_id
 * @property Carbon $date
 * @property Carbon|null $start_time
 * @property Carbon|null $end_time
 * @property float|null $total_hours
 * @property float|null $overtime_hours
 * @property int|null $batch_id
 * @property string|null $task_description
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Batch|null $batch
 * @property User $user
 */
class WorkLog extends Model
{
    protected $table = 'work_logs';

    protected $casts = [
        'user_id' => 'int',
        'date' => 'datetime',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'total_hours' => 'float',
        'overtime_hours' => 'float',
        'batch_id' => 'int',
    ];

    protected $fillable = [
        'user_id',
        'date',
        'start_time',
        'end_time',
        'total_hours',
        'overtime_hours',
        'batch_id',
        'task_description',
        'notes',
    ];

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
