<?php

namespace App\Models;

use App\Enums\RatingDirection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rating extends Model
{
    protected $fillable = [
        'order_id',
        'doctor_id',
        'lab_id',
        'score',
        'review',
        'direction',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'integer',
            'direction' => RatingDirection::class,
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function lab(): BelongsTo
    {
        return $this->belongsTo(Lab::class);
    }
}
