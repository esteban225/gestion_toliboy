<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class FormResponseValue
 *
 * @property int $id
 * @property int $response_id
 * @property int $field_id
 * @property string|null $value
 * @property string|null $file_path
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property FormField $form_field
 * @property FormResponse $form_response
 */
class FormResponseValue extends Model
{
    protected $table = 'form_response_values';

    protected $casts = [
        'response_id' => 'int',
        'field_id' => 'int',
    ];

    protected $fillable = [
        'response_id',
        'field_id',
        'value',
        'file_path',
    ];

    public function form_field()
    {
        return $this->belongsTo(FormField::class, 'field_id');
    }

    public function form_response()
    {
        return $this->belongsTo(FormResponse::class, 'response_id');
    }
}
