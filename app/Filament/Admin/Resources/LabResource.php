<?php

namespace App\Filament\Admin\Resources;

use App\Enums\ApprovalStatus;
use App\Filament\Admin\Resources\LabResource\Pages;
use App\Models\Lab;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LabResource extends Resource
{
    public static function getNavigationGroup(): ?string
    {
        return __('dentalink.nav.groups.management');
    }

    protected static ?string $model = Lab::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    

    protected static ?int $navigationSort = 2;

    public static function getModelLabel(): string
    {
        return __('dentalink.models.lab');
    }

    public static function getPluralModelLabel(): string
    {
        return __('dentalink.models.labs');
    }

    public static function getNavigationLabel(): string
    {
        return __('dentalink.resources.labs.nav');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('dentalink.sections.lab_information'))
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label(__('dentalink.fields.owner'))
                            ->relationship('user', 'name')
                            ->searchable(),
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
                        Forms\Components\TextInput::make('rating')
                            ->numeric()
                            ->step(0.1),
                        Forms\Components\TextInput::make('avg_turnaround_days')
                            ->numeric(),
                        Forms\Components\TextInput::make('starting_price')
                            ->numeric()
                            ->prefix('$'),
                        Forms\Components\Select::make('approval_status')
                            ->options(collect(ApprovalStatus::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()]))
                            ->required(),
                        Forms\Components\Toggle::make('is_featured'),
                        Forms\Components\Toggle::make('is_active')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('country'),
                Tables\Columns\TextColumn::make('approval_status')
                    ->badge()
                    ->formatStateUsing(fn (?ApprovalStatus $state) => $state?->label())
                    ->color(fn (?ApprovalStatus $state) => match ($state) {
                        ApprovalStatus::Approved => 'success',
                        ApprovalStatus::Pending => 'warning',
                        ApprovalStatus::Rejected => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('rating')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->date('M j, Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('approval_status')
                    ->options(collect(ApprovalStatus::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()])),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label(__('dentalink.actions.approve'))
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Lab $record) => $record->approval_status !== ApprovalStatus::Approved)
                    ->action(function (Lab $record) {
                        $record->update(['approval_status' => ApprovalStatus::Approved, 'is_active' => true]);
                        Notification::make()->title(__('dentalink.notifications.lab_approved'))->success()->send();
                    }),
                Tables\Actions\Action::make('reject')
                    ->label(__('dentalink.actions.reject'))
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Lab $record) => $record->approval_status !== ApprovalStatus::Rejected)
                    ->requiresConfirmation()
                    ->action(function (Lab $record) {
                        $record->update(['approval_status' => ApprovalStatus::Rejected, 'is_active' => false]);
                        Notification::make()->title(__('dentalink.notifications.lab_rejected'))->warning()->send();
                    }),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLabs::route('/'),
            'create' => Pages\CreateLab::route('/create'),
            'edit' => Pages\EditLab::route('/{record}/edit'),
        ];
    }
}
