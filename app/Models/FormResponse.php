<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class FormResponse
 *
 * @property int $id
 * @property int $form_id
 * @property int $user_id
 * @property int|null $batch_id
 * @property string $status
 * @property Carbon|null $submitted_at
 * @property int|null $reviewed_by
 * @property Carbon|null $reviewed_at
 * @property string|null $review_notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Batch|null $batch
 * @property Form $form
 * @property User $user
 * @property Collection|FormResponseValue[] $form_response_values
 */
class FormResponse extends Model
{
    protected $table = 'form_responses';

    protected $casts = [
        'form_id' => 'int',
        'user_id' => 'int',
        'batch_id' => 'int',
        'submitted_at' => 'datetime',
        'reviewed_by' => 'int',
        'reviewed_at' => 'datetime',
    ];

    protected $fillable = [
        'form_id',
        'user_id',
        'batch_id',
        'status',
        'submitted_at',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
    ];

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function form_response_values()
    {
        return $this->hasMany(FormResponseValue::class, 'response_id');
    }
}
