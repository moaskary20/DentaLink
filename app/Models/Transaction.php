<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = [
        'wallet_id',
        'order_id',
        'type',
        'description',
        'amount',
        'status',
        'reference',
    ];

    protected function casts(): array
    {
        return [
            'type' => TransactionType::class,
            'status' => PaymentStatus::class,
            'amount' => 'decimal:2',
        ];
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
