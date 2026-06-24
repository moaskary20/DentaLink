<?php

namespace App\Filament\App\Pages;

use App\Models\PaymentMethod;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\PaymentService;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class WalletPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-wallet';

    protected static string $view = 'filament.app.pages.wallet-page';

    public static function getNavigationGroup(): ?string
    {
        return __('dentalink.nav.groups.finance');
    }

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('dentalink.pages.wallet.nav');
    }

    public function getTitle(): string
    {
        return __('dentalink.pages.wallet.title');
    }

    public float $depositAmount = 500;

    public float $withdrawAmount = 100;

    public function getWallet(): ?Wallet
    {
        return Wallet::query()->firstOrCreate(
            ['user_id' => Auth::id()],
            ['balance' => 0, 'currency' => 'USD']
        );
    }

    public function getBalance(): float
    {
        return (float) $this->getWallet()->balance;
    }

    public function getTotalPaid(): float
    {
        return (float) Transaction::query()
            ->whereHas('wallet', fn ($q) => $q->where('user_id', Auth::id()))
            ->where('amount', '<', 0)
            ->sum('amount') * -1;
    }

    public function getPendingAmount(): float
    {
        return app(PaymentService::class)->pendingInvoiceTotal(Auth::user());
    }

    public function deposit(): void
    {
        try {
            app(PaymentService::class)->deposit(Auth::user(), $this->depositAmount, 'Visa/Mastercard');
            Notification::make()->title(__('dentalink.notifications.deposit_success'))->success()->send();
        } catch (\Throwable $e) {
            Notification::make()->title(__('dentalink.notifications.deposit_failed'))->body($e->getMessage())->danger()->send();
        }
    }

    public function withdraw(): void
    {
        try {
            app(PaymentService::class)->withdraw(Auth::user(), $this->withdrawAmount);
            Notification::make()->title(__('dentalink.notifications.withdrawal_success'))->success()->send();
        } catch (\Throwable $e) {
            Notification::make()->title(__('dentalink.notifications.withdrawal_failed'))->body($e->getMessage())->danger()->send();
        }
    }

    public function addCard(string $type = 'visa'): void
    {
        $labels = [
            'visa' => __('dentalink.pages.wallet.payment_methods.visa', ['last4' => random_int(1000, 9999)]),
            'mastercard' => __('dentalink.pages.wallet.payment_methods.mastercard', ['last4' => random_int(1000, 9999)]),
            'apple_pay' => __('dentalink.pages.wallet.payment_methods.apple_pay'),
            'google_pay' => __('dentalink.pages.wallet.payment_methods.google_pay'),
        ];

        app(PaymentService::class)->addPaymentMethod(
            Auth::user(),
            $type,
            $labels[$type] ?? ucfirst($type),
            in_array($type, ['visa', 'mastercard']) ? substr($labels[$type], -4) : null
        );

        Notification::make()->title(__('dentalink.notifications.payment_method_added'))->success()->send();
    }

    public function getPaymentMethods()
    {
        return PaymentMethod::query()
            ->where('user_id', Auth::id())
            ->orderByDesc('is_default')
            ->get();
    }

    public function getTransactions()
    {
        return Transaction::query()
            ->with('order')
            ->whereHas('wallet', fn ($q) => $q->where('user_id', Auth::id()))
            ->latest()
            ->limit(10)
            ->get();
    }
}
