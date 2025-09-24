<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class VBatchesLeadTime
 *
 * @property int $batch_id
 * @property string $code
 * @property string|null $product_name
 * @property Carbon $start_date
 * @property Carbon|null $expected_end_date
 * @property Carbon|null $actual_end_date
 * @property int|null $days_to_complete
 * @property int|null $delay_vs_expected_days
 */
class VBatchesLeadTime extends Model
{
    protected $table = 'v_batches_lead_times';

    public $incrementing = false;

    public $timestamps = false;

    protected $casts = [
        'batch_id' => 'int',
        'start_date' => 'datetime',
        'expected_end_date' => 'datetime',
        'actual_end_date' => 'datetime',
        'days_to_complete' => 'int',
        'delay_vs_expected_days' => 'int',
    ];

    protected $fillable = [
        'batch_id',
        'code',
        'product_name',
        'start_date',
        'expected_end_date',
        'actual_end_date',
        'days_to_complete',
        'delay_vs_expected_days',
    ];
}
