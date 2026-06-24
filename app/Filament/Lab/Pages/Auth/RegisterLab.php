<?php

namespace App\Filament\Lab\Pages\Auth;

use App\Services\ApprovalService;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Facades\Filament;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Http\Responses\Auth\Contracts\RegistrationResponse;
use Filament\Notifications\Notification;
use Filament\Pages\Auth\Register as BaseRegister;
use Illuminate\Database\Eloquent\Model;

class RegisterLab extends BaseRegister
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('lab_name')->label(__('dentalink.auth.lab_name'))->required(),
                TextInput::make('contact_name')->label(__('dentalink.auth.contact_person'))->required(),
                TextInput::make('email')->email()->required()->unique('users'),
                TextInput::make('phone')->tel(),
                TextInput::make('country')->required(),
                TextInput::make('city'),
                TagsInput::make('specialties')->placeholder(__('dentalink.auth.specialties_placeholder')),
                FileUpload::make('license_file')->label(__('dentalink.auth.lab_license'))->directory('lab-licenses')->required(),
                TextInput::make('starting_price')->numeric()->prefix('$')->default(200),
                TextInput::make('avg_turnaround_days')->numeric()->default(7)->suffix('days'),
                Select::make('locale')->options(\App\Support\DentaLinkLocale::labels())->default('en'),
                TextInput::make('password')->password()->required(),
                TextInput::make('passwordConfirmation')->password()->required()->same('password'),
            ]);
    }

    protected function handleRegistration(array $data): Model
    {
        return app(ApprovalService::class)->registerLab($data);
    }

    public function register(): ?RegistrationResponse
    {
        try {
            $this->rateLimit(2);
        } catch (TooManyRequestsException $exception) {
            Notification::make()->title(__('dentalink.notifications.too_many_attempts'))->danger()->send();

            return null;
        }

        $data = $this->form->getState();
        $user = $this->handleRegistration($data);

        Filament::auth()->login($user);

        Notification::make()
            ->title(__('dentalink.notifications.lab_registration_submitted'))
            ->body(__('dentalink.notifications.lab_registration_pending_body'))
            ->success()
            ->send();

        return app(RegistrationResponse::class);
    }
}
