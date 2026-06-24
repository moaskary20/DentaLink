<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Received = 'received';
    case InProgress = 'in_progress';
    case QualityReview = 'quality_review';
    case Shipping = 'shipping';
    case Delivered = 'delivered';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Received => __('dentalink.enums.order_status.received'),
            self::InProgress => __('dentalink.enums.order_status.in_progress'),
            self::QualityReview => __('dentalink.enums.order_status.quality_review'),
            self::Shipping => __('dentalink.enums.order_status.shipping'),
            self::Delivered => __('dentalink.enums.order_status.delivered'),
            self::Completed => __('dentalink.enums.order_status.completed'),
            self::Cancelled => __('dentalink.enums.order_status.cancelled'),
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Received => 'badge-gray',
            self::InProgress => 'badge-blue',
            self::QualityReview => 'badge-orange',
            self::Shipping => 'badge-teal',
            self::Delivered => 'badge-teal',
            self::Completed => 'badge-green',
            self::Cancelled => 'badge-red',
        };
    }
}
