<?php

namespace App\Enums;

enum TransactionType: string
{
    case Deposit = 'deposit';
    case Withdrawal = 'withdrawal';
    case Payment = 'payment';
    case Refund = 'refund';

    public function label(): string
    {
        return match ($this) {
            self::Deposit => __('dentalink.enums.transaction_type.deposit'),
            self::Withdrawal => __('dentalink.enums.transaction_type.withdrawal'),
            self::Payment => __('dentalink.enums.transaction_type.payment'),
            self::Refund => __('dentalink.enums.transaction_type.refund'),
        };
    }
}
