<?php

namespace App\Filament\Lab\Pages;

use App\Models\Lab;
use App\Support\CurrentLab;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class LabProfile extends Page implements HasForms
{
    use InteractsWithForms;
    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    public static function getNavigationLabel(): string
    {
        return __('dentalink.pages.lab_profile.nav');
    }
    public function getTitle(): string
    {
        return __('dentalink.pages.lab_profile.title');
    }

    protected static string $view = 'filament.lab.pages.lab-profile';

    

    protected static ?int $navigationSort = 1;

    public ?array $data = [];

    public function mount(): void
    {
        $lab = CurrentLab::get();

        if ($lab) {
            $this->form->fill($lab->toArray());
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('dentalink.sections.laboratory_information'))
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required(),
                        Forms\Components\Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('country'),
                        Forms\Components\TextInput::make('city'),
                        Forms\Components\TextInput::make('address')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('phone')
                            ->tel(),
                        Forms\Components\TextInput::make('email')
                            ->email(),
                        Forms\Components\TextInput::make('avg_turnaround_days')
                            ->numeric()
                            ->suffix('days')
                            ->label(__('dentalink.fields.average_turnaround')),
                        Forms\Components\TextInput::make('starting_price')
                            ->numeric()
                            ->prefix('$')
                            ->label(__('dentalink.fields.starting_price')),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $lab = CurrentLab::get();

        if (! $lab) {
            Notification::make()->title(__('dentalink.notifications.lab_profile_not_found'))->danger()->send();

            return;
        }

        $lab->update($this->form->getState());

        Notification::make()->title(__('dentalink.notifications.profile_updated'))->success()->send();
    }

    public function getLab(): ?Lab
    {
        return CurrentLab::get();
    }
}
