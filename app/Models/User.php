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
     * Los atributos que pueden ser asignados masivamente.
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

    /**
     * Carga siempre la relación 'role' para incluirla en los claims del JWT.
     *
     * @var array<string>
     */
    protected $with = ['role'];

    /**
     * Los atributos que deben ocultarse para serialización.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
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
     *
     * @return array<string, mixed> Claims personalizados: rol y permisos.
     */
    public function getJWTCustomClaims(): array
    {
        return [
            'role' => $this->role?->name
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
        return $this->belongsToMany(Notification::class, 'notification_user')
            ->withPivot(['is_read', 'read_at'])
            ->withTimestamps();
    }

    /**
     * Obtener todas las notificaciones del usuario (asignadas + globales)
     */
    public function allNotifications()
    {
        return Notification::forUser($this->id);
    }

    /**
     * Obtener notificaciones no leídas del usuario
     */
    public function unreadNotifications()
    {
        return Notification::unreadForUser($this->id);
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

    /**
     * Check if user has a specific role.
     */
    public function hasRole(string $roleName): bool
    {
        return $this->role && $this->role->name === $roleName;
    }

    /**
     * Check if user has any of the given roles.
     */
    public function hasAnyRole(array $roles): bool
    {
        return $this->role && in_array($this->role->name, $roles);
    }

    /**
     * Get user permissions based on role.
     */
    public function getPermissions(): array
    {
        if (! $this->role) {
            return [];
        }

        // This method would ideally be moved to a dedicated Permission service
        $permissions = [
            'DEV' => ['*'], // All permissions
            'GG' => ['dashboard.view', 'reports.read', 'reports.export'],
            'INPL' => ['forms.manage', 'work_logs.manage', 'dashboard.view'],
            'INPR' => ['forms.manage', 'work_logs.manage', 'dashboard.view'],
            'TRZ' => ['forms.read', 'batches.read', 'reports.read'],
            'OP' => ['forms.create', 'work_logs.create'],
        ];

        return $permissions[$this->role->name] ?? [];
    }

    /**
     * Check if user has specific permission.
     */
    public function hasPermission(string $permission): bool
    {
        $permissions = $this->getPermissions();

        return in_array('*', $permissions) || in_array($permission, $permissions);
    }
}
