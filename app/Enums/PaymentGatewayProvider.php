<?php

namespace App\Enums;

enum PaymentGatewayProvider: string
{
    case Stripe = 'stripe';
    case Paypal = 'paypal';
    case Wallet = 'wallet';

    public function label(): string
    {
        return match ($this) {
            self::Stripe => __('dentalink.payment_gateways.stripe'),
            self::Paypal => __('dentalink.payment_gateways.paypal'),
            self::Wallet => __('dentalink.payment_gateways.wallet'),
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Stripe => '💳',
            self::Paypal => '🅿️',
            self::Wallet => '👛',
        };
    }
}
