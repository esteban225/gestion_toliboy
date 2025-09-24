<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PersonalDatum
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $num_phone
 * @property string|null $num_phone_alt
 * @property string|null $num_identification
 * @property string|null $identification_type
 * @property string|null $address
 * @property string|null $emergency_contact
 * @property string|null $emergency_phone
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property User $user
 */
class PersonalDatum extends Model
{
    protected $table = 'personal_data';

    protected $casts = [
        'user_id' => 'int',
    ];

    protected $fillable = [
        'user_id',
        'num_phone',
        'num_phone_alt',
        'num_identification',
        'identification_type',
        'address',
        'emergency_contact',
        'emergency_phone',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
