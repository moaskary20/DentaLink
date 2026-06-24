<?php

namespace App\Enums;

enum ApprovalStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Pending => __('dentalink.enums.approval_status.pending'),
            self::Approved => __('dentalink.enums.approval_status.approved'),
            self::Rejected => __('dentalink.enums.approval_status.rejected'),
        };
    }
}
