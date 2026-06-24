<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderAttachment extends Model
{
    protected $fillable = [
        'order_id',
        'path',
        'type',
        'original_name',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
