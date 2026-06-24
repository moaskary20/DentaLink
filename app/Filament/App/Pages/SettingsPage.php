<?php

namespace App\Filament\App\Pages;

use App\Models\DoctorProfile;
use App\Services\AiAssistantService;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SettingsPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    public static function getNavigationLabel(): string
    {
        return __('dentalink.pages.settings.nav');
    }
    public function getTitle(): string
    {
        return __('dentalink.pages.settings.title');
    }

    protected static string $view = 'filament.app.pages.settings-page';

    

    protected static ?int $navigationSort = 1;

    public ?array $data = [];

    public function mount(): void
    {
        $user = Auth::user();
        $profile = $user->doctorProfile;

        $this->form->fill([
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'country' => $user->country,
            'locale' => $user->locale ?? 'en',
            'clinic_name' => $profile?->clinic_name,
            'specialization' => $profile?->specialization,
            'license_number' => $profile?->license_number,
            'license_file' => $profile?->license_file,
            'bio' => $profile?->bio,
        ]);

        app()->setLocale($user->locale ?? 'en');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('dentalink.sections.profile'))
                    ->schema([
                        Forms\Components\TextInput::make('name')->required(),
                        Forms\Components\TextInput::make('email')->email()->required(),
                        Forms\Components\TextInput::make('phone')->tel(),
                        Forms\Components\TextInput::make('country'),
                        Forms\Components\Select::make('locale')
                            ->label(__('dentalink.fields.language'))
                            ->options(\App\Support\DentaLinkLocale::labels()),
                    ])
                    ->columns(2),
                Forms\Components\Section::make(__('dentalink.sections.professional_profile'))
                    ->schema([
                        Forms\Components\TextInput::make('clinic_name'),
                        Forms\Components\TextInput::make('specialization'),
                        Forms\Components\TextInput::make('license_number'),
                        Forms\Components\FileUpload::make('license_file')
                            ->label(__('dentalink.auth.license_certificate'))
                            ->directory('doctor-licenses'),
                        Forms\Components\Textarea::make('bio')->rows(3)->columnSpanFull(),
                    ])
                    ->columns(2),
                Forms\Components\Section::make(__('dentalink.sections.security'))
                    ->schema([
                        Forms\Components\TextInput::make('new_password')->password()->dehydrated(false),
                        Forms\Components\TextInput::make('new_password_confirmation')->password()->dehydrated(false),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $user = Auth::user();

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'country' => $data['country'] ?? null,
            'locale' => $data['locale'] ?? 'en',
        ]);

        if (! empty($data['new_password'])) {
            $user->update(['password' => Hash::make($data['new_password'])]);
        }

        DoctorProfile::query()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'clinic_name' => $data['clinic_name'] ?? null,
                'specialization' => $data['specialization'] ?? null,
                'license_number' => $data['license_number'] ?? null,
                'license_file' => $data['license_file'] ?? null,
                'bio' => $data['bio'] ?? null,
            ]
        );

        app()->setLocale($data['locale'] ?? 'en');

        Notification::make()->title(__('dentalink.notifications.settings_saved'))->success()->send();
    }
}
