<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * Class User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property int|null $role_id
 * @property string|null $position
 * @property bool $is_active
 * @property Carbon|null $last_login
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Role|null $role
 * @property Collection|AuditLog[] $audit_logs
 * @property Collection|Batch[] $batches
 * @property Collection|CurrentUserSession[] $current_user_sessions
 * @property Collection|FormResponse[] $form_responses
 * @property Collection|Form[] $forms
 * @property Collection|InventoryMovement[] $inventory_movements
 * @property Collection|Notification[] $notifications
 * @property PersonalDatum|null $personal_datum
 * @property Collection|Product[] $products
 * @property Collection|RawMaterial[] $raw_materials
 * @property Collection|WorkLog[] $work_logs
 */
class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'position',
        'is_active',
        'last_login',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     */
    public function getJWTCustomClaims()
    {
        return [
            'role' => $this->role?->name,
            'permissions' => $this->role?->name ? $this->getPermissions() : [],
        ];
    }

    public function current_user_sessions()
    {
        return $this->hasMany(CurrentUserSession::class);
    }

    public function form_responses()
    {
        return $this->hasMany(FormResponse::class);
    }

    public function forms()
    {
        return $this->hasMany(Form::class, 'created_by');
    }

    public function inventory_movements()
    {
        return $this->hasMany(InventoryMovement::class, 'created_by');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function personal_datum()
    {
        return $this->hasOne(PersonalDatum::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'created_by');
    }

    public function raw_materials()
    {
        return $this->hasMany(RawMaterial::class, 'created_by');
    }

    public function work_logs()
    {
        return $this->hasMany(WorkLog::class);
    }
}
