<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderStage extends Model
{
    protected $fillable = [
        'order_id',
        'status',
        'label',
        'sort_order',
        'completed_at',
        'expected_at',
        'is_current',
        'doctor_approved_at',
        'lab_approved_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'completed_at' => 'datetime',
            'expected_at' => 'datetime',
            'is_current' => 'boolean',
            'doctor_approved_at' => 'datetime',
            'lab_approved_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
