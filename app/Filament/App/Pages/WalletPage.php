<?php

namespace App\Filament\App\Pages;

use App\Models\PaymentMethod;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\PaymentService;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

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

    public bool $showAddCardModal = false;

    public array $cardForm = [
        'type' => 'visa',
        'holder' => '',
        'number' => '',
        'expiry_month' => '',
        'expiry_year' => '',
        'is_default' => false,
    ];

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

    public function openAddCardModal(): void
    {
        $this->resetCardForm();
        $this->showAddCardModal = true;
    }

    public function closeAddCardModal(): void
    {
        $this->showAddCardModal = false;
        $this->resetCardForm();
    }

    public function saveCard(): void
    {
        $this->validate([
            'cardForm.type' => 'required|in:visa,mastercard',
            'cardForm.holder' => 'required|string|max:100',
            'cardForm.number' => 'required|string|min:13|max:23',
            'cardForm.expiry_month' => 'required|digits:2',
            'cardForm.expiry_year' => 'required|digits:2',
            'cardForm.is_default' => 'boolean',
        ], [
            'cardForm.type.required' => __('dentalink.blades.wallet.card_form.type_required'),
            'cardForm.holder.required' => __('dentalink.blades.wallet.card_form.holder_required'),
            'cardForm.number.required' => __('dentalink.blades.wallet.card_form.number_required'),
            'cardForm.expiry_month.required' => __('dentalink.blades.wallet.card_form.expiry_required'),
            'cardForm.expiry_year.required' => __('dentalink.blades.wallet.card_form.expiry_required'),
        ]);

        $digits = preg_replace('/\D/', '', $this->cardForm['number']) ?? '';

        if (strlen($digits) < 13 || strlen($digits) > 19) {
            throw ValidationException::withMessages([
                'cardForm.number' => __('dentalink.blades.wallet.card_form.number_invalid'),
            ]);
        }

        $month = (int) $this->cardForm['expiry_month'];

        if ($month < 1 || $month > 12) {
            throw ValidationException::withMessages([
                'cardForm.expiry_month' => __('dentalink.blades.wallet.card_form.expiry_invalid'),
            ]);
        }

        $expiry = Carbon::createFromDate(2000 + (int) $this->cardForm['expiry_year'], $month, 1)->endOfMonth();

        if ($expiry->isPast()) {
            throw ValidationException::withMessages([
                'cardForm.expiry_year' => __('dentalink.blades.wallet.card_form.expiry_past'),
            ]);
        }

        $type = $this->cardForm['type'];
        $lastFour = substr($digits, -4);
        $label = match ($type) {
            'visa' => 'Visa',
            'mastercard' => 'Mastercard',
            default => ucfirst($type),
        };

        app(PaymentService::class)->addPaymentMethod(
            Auth::user(),
            $type,
            $label,
            $lastFour,
            (bool) $this->cardForm['is_default'],
        );

        $this->closeAddCardModal();

        Notification::make()
            ->title(__('dentalink.notifications.payment_method_added'))
            ->success()
            ->send();
    }

    public function addCard(string $type = 'visa'): void
    {
        $labels = [
            'visa' => 'Visa',
            'mastercard' => 'Mastercard',
            'apple_pay' => __('dentalink.pages.wallet.payment_methods.apple_pay'),
            'google_pay' => __('dentalink.pages.wallet.payment_methods.google_pay'),
        ];

        app(PaymentService::class)->addPaymentMethod(
            Auth::user(),
            $type,
            $labels[$type] ?? ucfirst($type),
            in_array($type, ['visa', 'mastercard']) ? (string) random_int(1000, 9999) : null,
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

    protected function resetCardForm(): void
    {
        $this->cardForm = [
            'type' => 'visa',
            'holder' => '',
            'number' => '',
            'expiry_month' => '',
            'expiry_year' => '',
            'is_default' => false,
        ];

        $this->resetValidation();
    }
}
