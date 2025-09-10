<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

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
 * 
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
 *
 * @package App\Models
 */
class User extends Model
{
	protected $table = 'users';

	protected $casts = [
		'role_id' => 'int',
		'is_active' => 'bool',
		'last_login' => 'datetime'
	];

	protected $hidden = [
		'password'
	];

	protected $fillable = [
		'name',
		'email',
		'password',
		'role_id',
		'position',
		'is_active',
		'last_login'
	];

	public function role()
	{
		return $this->belongsTo(Role::class);
	}

	public function audit_logs()
	{
		return $this->hasMany(AuditLog::class);
	}

	public function batches()
	{
		return $this->hasMany(Batch::class, 'created_by');
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
