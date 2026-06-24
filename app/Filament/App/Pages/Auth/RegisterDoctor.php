<?php

namespace App\Filament\App\Pages\Auth;

use App\Services\ApprovalService;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Facades\Filament;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Http\Responses\Auth\Contracts\RegistrationResponse;
use Filament\Notifications\Notification;
use Filament\Pages\Auth\Register as BaseRegister;
use Illuminate\Database\Eloquent\Model;

class RegisterDoctor extends BaseRegister
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required()->maxLength(255),
                TextInput::make('email')->email()->required()->maxLength(255)->unique('users'),
                TextInput::make('phone')->tel(),
                TextInput::make('country'),
                TextInput::make('clinic_name')->label(__('dentalink.auth.clinic_name')),
                TextInput::make('specialization'),
                TextInput::make('license_number')->label(__('dentalink.auth.license_number')),
                FileUpload::make('license_file')->label(__('dentalink.auth.license_certificate'))->directory('doctor-licenses'),
                Select::make('locale')->options(\App\Support\DentaLinkLocale::labels())->default('en'),
                TextInput::make('password')->password()->required(),
                TextInput::make('passwordConfirmation')->password()->required()->same('password'),
            ]);
    }

    protected function handleRegistration(array $data): Model
    {
        return app(ApprovalService::class)->registerDoctor($data);
    }

    public function register(): ?RegistrationResponse
    {
        try {
            $this->rateLimit(2);
        } catch (TooManyRequestsException $exception) {
            Notification::make()
                ->title(__('filament-panels::pages/auth/register.notifications.throttled.title'))
                ->danger()
                ->send();

            return null;
        }

        $data = $this->form->getState();
        $user = $this->handleRegistration($data);

        Filament::auth()->login($user);

        Notification::make()
            ->title(__('dentalink.notifications.registration_submitted'))
            ->body(__('dentalink.notifications.doctor_registration_pending_body'))
            ->success()
            ->send();

        return app(RegistrationResponse::class);
    }
}
