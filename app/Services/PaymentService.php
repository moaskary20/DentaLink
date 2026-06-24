<?php

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Enums\TransactionType;
use App\Models\PaymentMethod;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;

class PaymentService
{
    public function deposit(User $user, float $amount, string $method = 'wallet'): Transaction
    {
        $wallet = $this->walletFor($user);
        $wallet->increment('balance', $amount);

        return Transaction::query()->create([
            'wallet_id' => $wallet->id,
            'type' => TransactionType::Deposit,
            'description' => "Deposit via {$method}",
            'amount' => $amount,
            'status' => PaymentStatus::Completed,
        ]);
    }

    public function withdraw(User $user, float $amount): Transaction
    {
        $wallet = $this->walletFor($user);

        if ($wallet->balance < $amount) {
            throw new \InvalidArgumentException('Insufficient balance.');
        }

        $wallet->decrement('balance', $amount);

        return Transaction::query()->create([
            'wallet_id' => $wallet->id,
            'type' => TransactionType::Withdrawal,
            'description' => 'Withdrawal to bank account',
            'amount' => -$amount,
            'status' => PaymentStatus::Completed,
        ]);
    }

    public function addPaymentMethod(User $user, string $type, string $label, ?string $lastFour = null): PaymentMethod
    {
        return PaymentMethod::query()->create([
            'user_id' => $user->id,
            'type' => $type,
            'label' => $label,
            'last_four' => $lastFour,
            'is_default' => PaymentMethod::query()->where('user_id', $user->id)->count() === 0,
        ]);
    }

    public function pendingInvoiceTotal(User $user): float
    {
        return (float) \App\Models\Invoice::query()
            ->where('doctor_id', $user->id)
            ->where('status', 'pending')
            ->sum('total');
    }

    protected function walletFor(User $user): Wallet
    {
        return Wallet::query()->firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0, 'currency' => 'USD']
        );
    }
}
