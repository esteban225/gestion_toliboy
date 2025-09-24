<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class VFormReviewTimeHour
 *
 * @property int $response_id
 * @property string $form_name
 * @property int $user_id
 * @property Carbon|null $submitted_at
 * @property int|null $reviewed_by
 * @property Carbon|null $reviewed_at
 * @property int|null $review_time_hours
 */
class VFormReviewTimeHour extends Model
{
    protected $table = 'v_form_review_time_hours';

    public $incrementing = false;

    public $timestamps = false;

    protected $casts = [
        'response_id' => 'int',
        'user_id' => 'int',
        'submitted_at' => 'datetime',
        'reviewed_by' => 'int',
        'reviewed_at' => 'datetime',
        'review_time_hours' => 'int',
    ];

    protected $fillable = [
        'response_id',
        'form_name',
        'user_id',
        'submitted_at',
        'reviewed_by',
        'reviewed_at',
        'review_time_hours',
    ];
}
