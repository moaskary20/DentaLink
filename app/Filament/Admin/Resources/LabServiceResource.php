<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\LabServiceResource\Pages;
use App\Models\LabService;
use App\Support\LabServiceCategories;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LabServiceResource extends Resource
{
    public static function getNavigationGroup(): ?string
    {
        return __('dentalink.nav.groups.management');
    }

    protected static ?string $model = LabService::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

    public static function getNavigationLabel(): string
    {
        return __('dentalink.resources.lab_services.nav');
    }

    

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('lab_id')
                    ->relationship('lab', 'name')
                    ->searchable()
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->required(),
                Forms\Components\Select::make('category')
                    ->options(LabServiceCategories::options()),
                Forms\Components\TextInput::make('price')
                    ->numeric()
                    ->prefix('$')
                    ->required(),
                Forms\Components\TextInput::make('turnaround_days')
                    ->numeric()
                    ->required(),
                Forms\Components\Toggle::make('is_active')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('lab.name')
                    ->label(__('dentalink.fields.lab'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category')
                    ->badge(),
                Tables\Columns\TextColumn::make('price')
                    ->money('usd')
                    ->sortable(),
                Tables\Columns\TextColumn::make('turnaround_days')
                    ->label(__('dentalink.fields.days'))
                    ->suffix(' days'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('lab_id')
                    ->relationship('lab', 'name')
                    ->label(__('dentalink.fields.lab')),
                Tables\Filters\SelectFilter::make('category'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('lab.name');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLabServices::route('/'),
            'create' => Pages\CreateLabService::route('/create'),
            'edit' => Pages\EditLabService::route('/{record}/edit'),
        ];
    }
}
