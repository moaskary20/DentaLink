<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\LabResource\Pages;
use App\Models\Lab;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Enums\ApprovalStatus;

class LabResource extends Resource
{
    public static function getNavigationGroup(): ?string
    {
        return __('dentalink.nav.groups.laboratories');
    }

    protected static ?string $model = Lab::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    public static function getNavigationLabel(): string
    {
        return __('dentalink.resources.labs_directory.nav');
    }

    

    protected static ?int $navigationSort = 2;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('is_active', true)
            ->where('approval_status', ApprovalStatus::Approved);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('country')
                    ->searchable(),
                Tables\Columns\TextColumn::make('city'),
                Tables\Columns\TextColumn::make('rating')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => number_format((float) $state, 1)),
                Tables\Columns\TextColumn::make('avg_turnaround_days')
                    ->label(__('dentalink.fields.avg_days'))
                    ->suffix(' days'),
                Tables\Columns\TextColumn::make('starting_price')
                    ->label(__('dentalink.fields.from'))
                    ->money('usd'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->defaultSort('rating', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('dentalink.sections.lab_profile'))
                    ->schema([
                        Infolists\Components\TextEntry::make('name'),
                        Infolists\Components\TextEntry::make('country'),
                        Infolists\Components\TextEntry::make('city'),
                        Infolists\Components\TextEntry::make('rating')
                            ->formatStateUsing(fn ($state) => number_format((float) $state, 1) . ' / 5'),
                        Infolists\Components\TextEntry::make('avg_turnaround_days')
                            ->label(__('dentalink.fields.average_turnaround'))
                            ->suffix(' days'),
                        Infolists\Components\TextEntry::make('starting_price')
                            ->money('usd'),
                        Infolists\Components\TextEntry::make('description')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLabs::route('/'),
            'view' => Pages\ViewLab::route('/{record}'),
        ];
    }
}
