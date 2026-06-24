<?php

namespace App\Models;

use App\Enums\ApprovalStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lab extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'country',
        'city',
        'address',
        'phone',
        'email',
        'logo',
        'rating',
        'avg_turnaround_days',
        'starting_price',
        'approval_status',
        'is_featured',
        'is_active',
        'specialties',
        'license_file',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'decimal:1',
            'starting_price' => 'decimal:2',
            'approval_status' => ApprovalStatus::class,
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'specialties' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(LabService::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }
}
