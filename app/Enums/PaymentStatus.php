<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case Pending = 'pending';
    case Completed = 'completed';
    case Failed = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::Pending => __('dentalink.enums.payment_status.pending'),
            self::Completed => __('dentalink.enums.payment_status.completed'),
            self::Failed => __('dentalink.enums.payment_status.failed'),
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Pending => 'badge-orange',
            self::Completed => 'badge-green',
            self::Failed => 'badge-red',
        };
    }
}
