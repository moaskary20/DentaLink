<?php

namespace App\Models;

use App\Enums\PaymentGatewayProvider;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class PaymentCheckout extends Model
{
    protected $fillable = [
        'uuid',
        'doctor_id',
        'order_id',
        'gateway',
        'amount',
        'currency',
        'status',
        'order_payload',
        'gateway_session_id',
        'gateway_payment_id',
        'failure_reason',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'gateway' => PaymentGatewayProvider::class,
            'status' => PaymentStatus::class,
            'amount' => 'decimal:2',
            'order_payload' => 'array',
            'paid_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $checkout): void {
            $checkout->uuid ??= (string) Str::uuid();
        });
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function isPending(): bool
    {
        return $this->status === PaymentStatus::Pending;
    }

    public function markCompleted(?string $paymentId = null): void
    {
        $this->update([
            'status' => PaymentStatus::Completed,
            'gateway_payment_id' => $paymentId ?? $this->gateway_payment_id,
            'paid_at' => now(),
            'failure_reason' => null,
        ]);
    }

    public function markFailed(?string $reason = null): void
    {
        $this->update([
            'status' => PaymentStatus::Failed,
            'failure_reason' => $reason,
        ]);
    }
}
