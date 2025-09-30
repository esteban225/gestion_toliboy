<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class Notification
 *
 * @property int $id
 * @property string $title
 * @property string $message
 * @property string $type
 * @property string $scope
 * @property string|null $related_table
 * @property int|null $related_id
 * @property Carbon|null $expires_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Notification extends Model
{
    protected $table = 'notifications';

    // Constantes para los tipos de scope
    const SCOPE_INDIVIDUAL = 'individual';

    const SCOPE_GROUP = 'group';

    const SCOPE_GLOBAL = 'global';

    // Constantes para tipos de notificación
    const TYPE_INFO = 'info';

    const TYPE_WARNING = 'warning';

    const TYPE_ERROR = 'error';

    const TYPE_SUCCESS = 'success';

    protected $casts = [
        'related_id' => 'int',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $fillable = [
        'title',
        'message',
        'type',
        'scope',
        'is_read',
        'related_table',
        'related_id',
        'user_id',
        'expires_at',
    ];

    /**
     * Relación many-to-many con usuarios a través de tabla pivote.
     * Incluye información de lectura (is_read, read_at)
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'notification_user')
            ->withPivot(['is_read', 'read_at'])
            ->withTimestamps();
    }

    /**
     * Scope para filtrar notificaciones globales
     */
    public function scopeGlobal(Builder $query): Builder
    {
        return $query->where('scope', self::SCOPE_GLOBAL);
    }

    /**
     * Scope para filtrar notificaciones asignadas (individual o group)
     */
    public function scopeAssigned(Builder $query): Builder
    {
        return $query->whereIn('scope', [self::SCOPE_INDIVIDUAL, self::SCOPE_GROUP]);
    }

    /**
     * Scope para filtrar por tipo de scope específico
     */
    public function scopeByScope(Builder $query, string $scope): Builder
    {
        return $query->where('scope', $scope);
    }

    /**
     * Scope para filtrar notificaciones no expiradas
     */
    public function scopeNotExpired(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
                ->orWhere('expires_at', '>', now());
        });
    }

    /**
     * Scope para filtrar por tipo de notificación
     */
    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    /**
     * Scope para obtener notificaciones de un usuario específico
     * Incluye notificaciones globales + asignadas al usuario
     */
    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where(function ($q) use ($userId) {
            // Notificaciones globales
            $q->where('scope', self::SCOPE_GLOBAL)
              // O notificaciones asignadas al usuario
                ->orWhereHas('users', function ($userQuery) use ($userId) {
                    $userQuery->where('user_id', $userId);
                });
        });
    }

    /**
     * Scope para notificaciones no leídas por un usuario
     */
    public function scopeUnreadForUser(Builder $query, int $userId): Builder
    {
        return $query->forUser($userId)
            ->where(function ($q) use ($userId) {
                // Globales (no tienen estado de lectura individual)
                $q->where('scope', self::SCOPE_GLOBAL)
                  // O asignadas y no leídas
                    ->orWhereHas('users', function ($userQuery) use ($userId) {
                        $userQuery->where('user_id', $userId)
                            ->where('is_read', false);
                    });
            });
    }

    /**
     * Verificar si la notificación ha expirado
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Verificar si es notificación global
     */
    public function isGlobal(): bool
    {
        return $this->scope === self::SCOPE_GLOBAL;
    }

    /**
     * Verificar si es notificación grupal
     */
    public function isGroup(): bool
    {
        return $this->scope === self::SCOPE_GROUP;
    }

    /**
     * Verificar si es notificación individual
     */
    public function isIndividual(): bool
    {
        return $this->scope === self::SCOPE_INDIVIDUAL;
    }

    /**
     * Obtener todos los scopes disponibles
     */
    public static function getScopes(): array
    {
        return [
            self::SCOPE_INDIVIDUAL,
            self::SCOPE_GROUP,
            self::SCOPE_GLOBAL,
        ];
    }

    /**
     * Obtener todos los tipos disponibles
     */
    public static function getTypes(): array
    {
        return [
            self::TYPE_INFO,
            self::TYPE_WARNING,
            self::TYPE_ERROR,
            self::TYPE_SUCCESS,
        ];
    }
}
