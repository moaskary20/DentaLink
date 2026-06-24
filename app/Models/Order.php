<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'doctor_id',
        'lab_id',
        'lab_service_id',
        'service_name',
        'tooth_number',
        'material',
        'shade',
        'status',
        'cost',
        'commission',
        'total',
        'is_express',
        'turnaround_days',
        'expected_delivery_at',
        'delivered_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'cost' => 'decimal:2',
            'commission' => 'decimal:2',
            'total' => 'decimal:2',
            'is_express' => 'boolean',
            'expected_delivery_at' => 'datetime',
            'delivered_at' => 'datetime',
        ];
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function lab(): BelongsTo
    {
        return $this->belongsTo(Lab::class);
    }

    public function labService(): BelongsTo
    {
        return $this->belongsTo(LabService::class);
    }

    public function stages(): HasMany
    {
        return $this->hasMany(OrderStage::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(OrderLog::class);
    }

    public function rating(): HasOne
    {
        return $this->hasOne(Rating::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(OrderAttachment::class);
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }
}
