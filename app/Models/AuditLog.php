<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class AuditLog
 *
 * @property int $id
 * @property string $table_name
 * @property int $record_id
 * @property string $action
 * @property array|null $old_values
 * @property array|null $new_values
 * @property int|null $user_id
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property Carbon|null $created_at
 * @property User|null $user
 */
class AuditLog extends Model
{
    protected $table = 'audit_logs';

    public $timestamps = false;

    protected $casts = [
        'record_id' => 'int',
        'old_values' => 'json',
        'new_values' => 'json',
        'user_id' => 'int',
    ];

    protected $fillable = [
        'table_name',
        'record_id',
        'action',
        'old_values',
        'new_values',
        'user_id',
        'ip_address',
        'user_agent',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
