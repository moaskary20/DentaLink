<?php

namespace App\Models;

use App\Enums\PaymentGatewayProvider;
use Illuminate\Database\Eloquent\Model;

class PaymentGateway extends Model
{
    protected $fillable = [
        'provider',
        'is_enabled',
        'is_sandbox',
        'public_key',
        'secret_key',
        'webhook_secret',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'provider' => PaymentGatewayProvider::class,
            'is_enabled' => 'boolean',
            'is_sandbox' => 'boolean',
            'public_key' => 'encrypted',
            'secret_key' => 'encrypted',
            'webhook_secret' => 'encrypted',
            'meta' => 'array',
        ];
    }

    public static function for(PaymentGatewayProvider $provider): self
    {
        return static::query()->firstOrCreate(
            ['provider' => $provider->value],
            [
                'is_enabled' => false,
                'is_sandbox' => true,
            ]
        );
    }

    public function isConfigured(): bool
    {
        return filled($this->public_key) && filled($this->secret_key);
    }

    public function isReady(): bool
    {
        return $this->is_enabled && $this->isConfigured();
    }
}
