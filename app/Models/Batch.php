<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Batch
 *
 * @property int $id
 * @property string $name
 * @property string $code
 * @property int|null $product_id
 * @property Carbon $start_date
 * @property Carbon|null $expected_end_date
 * @property Carbon|null $actual_end_date
 * @property string $status
 * @property int $quantity
 * @property int|null $defect_quantity
 * @property string|null $notes
 * @property int|null $created_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property User|null $user
 * @property Product|null $product
 * @property Collection|FormResponse[] $form_responses
 * @property Collection|InventoryMovement[] $inventory_movements
 * @property Collection|WorkLog[] $work_logs
 */
class Batch extends Model
{
    protected $table = 'batches';

    protected $casts = [
        'product_id' => 'int',
        'start_date' => 'datetime',
        'expected_end_date' => 'datetime',
        'actual_end_date' => 'datetime',
        'quantity' => 'int',
        'defect_quantity' => 'int',
        'created_by' => 'int',
    ];

    protected $fillable = [
        'name',
        'code',
        'product_id',
        'start_date',
        'expected_end_date',
        'actual_end_date',
        'status',
        'quantity',
        'defect_quantity',
        'notes',
        'created_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function form_responses()
    {
        return $this->hasMany(FormResponse::class);
    }

    public function inventory_movements()
    {
        return $this->hasMany(InventoryMovement::class);
    }

    public function work_logs()
    {
        return $this->hasMany(WorkLog::class);
    }
}
