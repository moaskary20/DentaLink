<?php

namespace App\Filament\App\Pages;

use App\Enums\ApprovalStatus;
use App\Enums\PaymentGatewayProvider;
use App\Models\Lab;
use App\Models\LabService;
use App\Services\AiAssistantService;
use App\Services\CommissionService;
use App\Services\OrderWorkflowService;
use App\Services\PaymentGatewayService;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class CreateOrder extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-plus-circle';

    protected static string $view = 'filament.app.pages.create-order';

    public static function getNavigationGroup(): ?string
    {
        return __('dentalink.nav.groups.order_management');
    }

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('dentalink.pages.create_order.nav');
    }

    public function getTitle(): string
    {
        return __('dentalink.pages.create_order.title');
    }

    public int $currentStep = 1;

    public ?array $data = [];

    public array $aiValidation = [];

    public function mount(): void
    {
        $defaultMethod = collect(app(PaymentGatewayService::class)->availableMethods())
            ->first()?->value ?? PaymentGatewayProvider::Wallet->value;

        $this->form->fill([
            'is_express' => false,
            'shade' => 'A2',
            'material' => 'Zirconia',
            'payment_method' => $defaultMethod,
        ]);

        if ($error = session('error')) {
            Notification::make()
                ->title(__('dentalink.notifications.order_create_failed'))
                ->body($error)
                ->danger()
                ->send();
        }

        if (session('success')) {
            Notification::make()
                ->title(session('success'))
                ->success()
                ->send();
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('tooth_number')
                    ->label(__('dentalink.fields.tooth_area'))
                    ->placeholder(__('dentalink.fields.tooth_area_placeholder'))
                    ->required()
                    ->visible(fn () => $this->currentStep === 1),
                Forms\Components\Select::make('material')
                    ->label(__('dentalink.fields.material'))
                    ->options([
                        'Zirconia' => __('dentalink.materials.zirconia'),
                        'PFM' => __('dentalink.materials.pfm_full'),
                        'E-Max' => __('dentalink.materials.emax'),
                        'Acrylic' => __('dentalink.materials.acrylic'),
                    ])
                    ->visible(fn () => $this->currentStep === 1),
                Forms\Components\Select::make('shade')
                    ->label(__('dentalink.fields.shade'))
                    ->options([
                        'A1' => 'A1', 'A2' => 'A2', 'A3' => 'A3', 'B1' => 'B1', 'B2' => 'B2',
                    ])
                    ->visible(fn () => $this->currentStep === 1),
                Forms\Components\DatePicker::make('expected_delivery_at')
                    ->label(__('dentalink.fields.required_delivery_date'))
                    ->visible(fn () => $this->currentStep === 1),
                Forms\Components\Toggle::make('is_express')
                    ->label(__('dentalink.fields.express_order_surcharge'))
                    ->visible(fn () => $this->currentStep === 1),
                Forms\Components\Textarea::make('notes')
                    ->label(__('dentalink.fields.additional_notes'))
                    ->rows(3)
                    ->visible(fn () => $this->currentStep === 1),
                Forms\Components\FileUpload::make('attachments')
                    ->label(__('dentalink.fields.dental_images'))
                    ->multiple()
                    ->directory('order-attachments')
                    ->acceptedFileTypes(['image/*', 'application/octet-stream', 'video/mp4', '.stl', '.obj', '.ply'])
                    ->visible(fn () => $this->currentStep === 2),
                Forms\Components\FileUpload::make('video')
                    ->label(__('dentalink.fields.case_video'))
                    ->directory('order-videos')
                    ->acceptedFileTypes(['video/mp4', 'video/quicktime'])
                    ->visible(fn () => $this->currentStep === 2),
                Forms\Components\Select::make('lab_id')
                    ->label(__('dentalink.fields.select_laboratory'))
                    ->options(fn () => Lab::query()
                        ->where('approval_status', ApprovalStatus::Approved)
                        ->where('is_active', true)
                        ->pluck('name', 'id'))
                    ->searchable()
                    ->live()
                    ->required()
                    ->visible(fn () => $this->currentStep === 3),
                Forms\Components\Select::make('lab_service_id')
                    ->label(__('dentalink.fields.service_type'))
                    ->options(fn (Get $get) => LabService::query()
                        ->when($get('lab_id'), fn ($q, $labId) => $q->where('lab_id', $labId))
                        ->where('is_active', true)
                        ->pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->visible(fn () => $this->currentStep === 3),
                Forms\Components\Radio::make('payment_method')
                    ->label(__('dentalink.fields.payment_method'))
                    ->options(fn () => collect(app(PaymentGatewayService::class)->availableMethodOptions())
                        ->mapWithKeys(fn (array $method) => [
                            $method['value'] => $method['icon'].' '.$method['label'],
                        ])
                        ->all())
                    ->required()
                    ->columns(1)
                    ->visible(fn () => $this->currentStep === 4),
            ])
            ->statePath('data');
    }

    public function nextStep(): void
    {
        if ($this->currentStep === 2) {
            $attachments = array_merge(
                $this->data['attachments'] ?? [],
                isset($this->data['video']) ? (array) $this->data['video'] : []
            );
            $this->aiValidation = app(AiAssistantService::class)->validateOrderFiles($attachments, $this->data);
        }

        if ($this->currentStep < 4) {
            $this->currentStep++;
        }
    }

    public function previousStep(): void
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function getEstimatedCost(): float
    {
        $serviceId = $this->data['lab_service_id'] ?? null;
        $base = $serviceId
            ? (float) LabService::query()->find($serviceId)?->price
            : 0;

        $express = ($this->data['is_express'] ?? false)
            ? app(CommissionService::class)->expressSurcharge()
            : 0;

        return $base + $express;
    }

    public function getCommission(): float
    {
        $lab = Lab::query()->find($this->data['lab_id'] ?? null);

        return app(CommissionService::class)->calculate(
            $this->getEstimatedCost(),
            (bool) ($this->data['is_express'] ?? false),
            (bool) $lab?->is_featured
        );
    }

    public function getTotal(): float
    {
        return $this->getEstimatedCost() + $this->getCommission();
    }

    public function getPredictedDelivery(): ?string
    {
        $service = LabService::query()->find($this->data['lab_service_id'] ?? null);

        if (! $service) {
            return null;
        }

        return app(AiAssistantService::class)
            ->predictDelivery($service, (bool) ($this->data['is_express'] ?? false))
            ->format('M j, Y');
    }

    public function getPaymentMethods(): array
    {
        return app(PaymentGatewayService::class)->availableMethodOptions();
    }

    public function submitOrder(): void
    {
        try {
            $data = $this->form->getState();
            $data['attachments'] = array_merge(
                $data['attachments'] ?? [],
                isset($data['video']) ? (array) $data['video'] : []
            );

            $paymentMethod = PaymentGatewayProvider::tryFrom($data['payment_method'] ?? '')
                ?? PaymentGatewayProvider::Wallet;

            unset($data['payment_method']);

            if ($paymentMethod === PaymentGatewayProvider::Wallet) {
                $order = app(OrderWorkflowService::class)->createOrder(
                    Auth::user(),
                    $data,
                    PaymentGatewayProvider::Wallet,
                );

                Notification::make()
                    ->title(__('dentalink.notifications.order_created'))
                    ->success()
                    ->send();

                $this->redirect(OrderTracking::getUrl(['order' => $order->order_number]));

                return;
            }

            $checkoutUrl = app(PaymentGatewayService::class)->initiateCheckout(
                Auth::user(),
                $data,
                $paymentMethod,
                $this->getTotal(),
            );

            Notification::make()
                ->title(__('dentalink.notifications.redirecting_to_payment'))
                ->success()
                ->send();

            $this->redirect($checkoutUrl, navigate: false);
        } catch (\Throwable $e) {
            Notification::make()
                ->title(__('dentalink.notifications.order_create_failed'))
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
