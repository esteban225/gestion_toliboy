<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class VUsersByRole
 * 
 * @property int $role_id
 * @property string $role_name
 * @property bool|null $is_active
 * @property int $user_count
 *
 * @package App\Models
 */
class VUsersByRole extends Model
{
	protected $table = 'v_users_by_role';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'role_id' => 'int',
		'is_active' => 'bool',
		'user_count' => 'int'
	];

	protected $fillable = [
		'role_id',
		'role_name',
		'is_active',
		'user_count'
	];
}
