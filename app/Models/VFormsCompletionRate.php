<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class VFormsCompletionRate
 *
 * @property int $form_id
 * @property string $form_name
 * @property string $form_version
 * @property int $total_responses
 * @property float|null $completed_responses
 * @property float|null $completion_rate_pct
 */
class VFormsCompletionRate extends Model
{
    protected $table = 'v_forms_completion_rate';

    public $incrementing = false;

    public $timestamps = false;

    protected $casts = [
        'form_id' => 'int',
        'total_responses' => 'int',
        'completed_responses' => 'float',
        'completion_rate_pct' => 'float',
    ];

    protected $fillable = [
        'form_id',
        'form_name',
        'form_version',
        'total_responses',
        'completed_responses',
        'completion_rate_pct',
    ];
}
