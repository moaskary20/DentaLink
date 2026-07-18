<?php

namespace App\Filament\Admin\Pages;

use App\Enums\PaymentGatewayProvider;
use App\Models\PaymentGateway;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class PaymentSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static string $view = 'filament.admin.pages.payment-settings';

    protected static ?int $navigationSort = 2;

    public ?array $data = [];

    public static function getNavigationGroup(): ?string
    {
        return __('dentalink.nav.groups.settings');
    }

    public static function getNavigationLabel(): string
    {
        return __('dentalink.pages.payment_settings.nav');
    }

    public function getTitle(): string
    {
        return __('dentalink.pages.payment_settings.title');
    }

    public function mount(): void
    {
        $stripe = PaymentGateway::for(PaymentGatewayProvider::Stripe);
        $paypal = PaymentGateway::for(PaymentGatewayProvider::Paypal);

        $this->form->fill([
            'stripe_is_enabled' => $stripe->is_enabled,
            'stripe_is_sandbox' => $stripe->is_sandbox,
            'stripe_public_key' => $stripe->public_key,
            'stripe_secret_key' => $stripe->secret_key,
            'stripe_webhook_secret' => $stripe->webhook_secret,
            'paypal_is_enabled' => $paypal->is_enabled,
            'paypal_is_sandbox' => $paypal->is_sandbox,
            'paypal_client_id' => $paypal->public_key,
            'paypal_secret' => $paypal->secret_key,
            'paypal_webhook_id' => $paypal->webhook_secret,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('dentalink.sections.stripe_gateway'))
                    ->description(__('dentalink.pages.payment_settings.stripe_help'))
                    ->schema([
                        Forms\Components\Toggle::make('stripe_is_enabled')
                            ->label(__('dentalink.fields.enable_gateway'))
                            ->inline(false),
                        Forms\Components\Toggle::make('stripe_is_sandbox')
                            ->label(__('dentalink.fields.sandbox_mode'))
                            ->inline(false)
                            ->helperText(__('dentalink.pages.payment_settings.sandbox_help')),
                        Forms\Components\TextInput::make('stripe_public_key')
                            ->label(__('dentalink.fields.stripe_publishable_key'))
                            ->placeholder('pk_test_...')
                            ->autocomplete(false),
                        Forms\Components\TextInput::make('stripe_secret_key')
                            ->label(__('dentalink.fields.stripe_secret_key'))
                            ->password()
                            ->revealable()
                            ->autocomplete(false),
                        Forms\Components\TextInput::make('stripe_webhook_secret')
                            ->label(__('dentalink.fields.stripe_webhook_secret'))
                            ->password()
                            ->revealable()
                            ->autocomplete(false)
                            ->helperText(__('dentalink.pages.payment_settings.webhook_optional')),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(__('dentalink.sections.paypal_gateway'))
                    ->description(__('dentalink.pages.payment_settings.paypal_help'))
                    ->schema([
                        Forms\Components\Toggle::make('paypal_is_enabled')
                            ->label(__('dentalink.fields.enable_gateway'))
                            ->inline(false),
                        Forms\Components\Toggle::make('paypal_is_sandbox')
                            ->label(__('dentalink.fields.sandbox_mode'))
                            ->inline(false)
                            ->helperText(__('dentalink.pages.payment_settings.sandbox_help')),
                        Forms\Components\TextInput::make('paypal_client_id')
                            ->label(__('dentalink.fields.paypal_client_id'))
                            ->autocomplete(false),
                        Forms\Components\TextInput::make('paypal_secret')
                            ->label(__('dentalink.fields.paypal_secret'))
                            ->password()
                            ->revealable()
                            ->autocomplete(false),
                        Forms\Components\TextInput::make('paypal_webhook_id')
                            ->label(__('dentalink.fields.paypal_webhook_id'))
                            ->autocomplete(false)
                            ->helperText(__('dentalink.pages.payment_settings.webhook_optional')),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        PaymentGateway::for(PaymentGatewayProvider::Stripe)->update([
            'is_enabled' => (bool) ($data['stripe_is_enabled'] ?? false),
            'is_sandbox' => (bool) ($data['stripe_is_sandbox'] ?? true),
            'public_key' => $data['stripe_public_key'] ?: null,
            'secret_key' => $data['stripe_secret_key'] ?: null,
            'webhook_secret' => $data['stripe_webhook_secret'] ?: null,
        ]);

        PaymentGateway::for(PaymentGatewayProvider::Paypal)->update([
            'is_enabled' => (bool) ($data['paypal_is_enabled'] ?? false),
            'is_sandbox' => (bool) ($data['paypal_is_sandbox'] ?? true),
            'public_key' => $data['paypal_client_id'] ?: null,
            'secret_key' => $data['paypal_secret'] ?: null,
            'webhook_secret' => $data['paypal_webhook_id'] ?: null,
        ]);

        Notification::make()
            ->title(__('dentalink.notifications.payment_settings_saved'))
            ->success()
            ->send();
    }
}
