<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class VBatchesByStatus
 *
 * @property string $status
 * @property int $batch_count
 */
class VBatchesByStatus extends Model
{
    protected $table = 'v_batches_by_status';

    public $incrementing = false;

    public $timestamps = false;

    protected $casts = [
        'batch_count' => 'int',
    ];

    protected $fillable = [
        'status',
        'batch_count',
    ];
}
