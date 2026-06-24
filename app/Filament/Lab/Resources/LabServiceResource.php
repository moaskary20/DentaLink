<?php

namespace App\Filament\Lab\Resources;

use App\Filament\Lab\Resources\LabServiceResource\Pages;
use App\Models\LabService;
use App\Support\CurrentLab;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LabServiceResource extends Resource
{
    public static function getNavigationGroup(): ?string
    {
        return __('dentalink.nav.groups.orders');
    }

    protected static ?string $model = LabService::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

    public static function getNavigationLabel(): string
    {
        return __('dentalink.resources.my_services.nav');
    }

    

    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): Builder
    {
        $labId = CurrentLab::id();

        return parent::getEloquentQuery()
            ->when($labId, fn ($q) => $q->where('lab_id', $labId), fn ($q) => $q->whereRaw('1 = 0'));
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('lab_id')
                    ->default(fn () => CurrentLab::id()),
                Forms\Components\TextInput::make('name')
                    ->required(),
                Forms\Components\Select::make('category')
                    ->options([
                        'Crown' => __('dentalink.service_categories.crown'),
                        'Bridge' => __('dentalink.service_categories.bridge'),
                        'Implant' => __('dentalink.service_categories.implant'),
                        'Veneer' => __('dentalink.service_categories.veneer'),
                        'Denture' => __('dentalink.service_categories.denture'),
                    ]),
                Forms\Components\TextInput::make('price')
                    ->numeric()
                    ->prefix('$')
                    ->required(),
                Forms\Components\TextInput::make('turnaround_days')
                    ->numeric()
                    ->suffix('days')
                    ->required(),
                Forms\Components\Toggle::make('is_active')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category')
                    ->badge(),
                Tables\Columns\TextColumn::make('price')
                    ->money('usd')
                    ->sortable(),
                Tables\Columns\TextColumn::make('turnaround_days')
                    ->suffix(' days')
                    ->label(__('dentalink.fields.turnaround')),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('dentalink.fields.active')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(fn (array $data) => array_merge($data, ['lab_id' => CurrentLab::id()])),
            ])
            ->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageLabServices::route('/'),
        ];
    }
}
