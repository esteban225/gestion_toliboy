<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class VFormFieldUsage
 *
 * @property int $form_id
 * @property string $form_name
 * @property int $field_id
 * @property string $field_label
 * @property string $field_code
 * @property string $field_type
 * @property int $responses_count
 * @property float|null $files_count
 */
class VFormFieldUsage extends Model
{
    protected $table = 'v_form_field_usage';

    public $incrementing = false;

    public $timestamps = false;

    protected $casts = [
        'form_id' => 'int',
        'field_id' => 'int',
        'responses_count' => 'int',
        'files_count' => 'float',
    ];

    protected $fillable = [
        'form_id',
        'form_name',
        'field_id',
        'field_label',
        'field_code',
        'field_type',
        'responses_count',
        'files_count',
    ];
}
