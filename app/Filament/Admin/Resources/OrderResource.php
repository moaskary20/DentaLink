<?php

namespace App\Filament\Admin\Resources;

use App\Enums\OrderStatus;
use App\Filament\Admin\Resources\OrderResource\Pages;
use App\Filament\Admin\Resources\OrderResource\RelationManagers\LogsRelationManager;
use App\Filament\Admin\Resources\OrderResource\RelationManagers\StagesRelationManager;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    public static function getNavigationGroup(): ?string
    {
        return __('dentalink.nav.groups.orders');
    }

    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    

    protected static ?int $navigationSort = 1;

    public static function getModelLabel(): string
    {
        return __('dentalink.models.order');
    }

    public static function getPluralModelLabel(): string
    {
        return __('dentalink.models.orders');
    }

    public static function getNavigationLabel(): string
    {
        return __('dentalink.resources.orders.nav');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('dentalink.sections.order_details'))
                    ->schema([
                        Forms\Components\TextInput::make('order_number')
                            ->required(),
                        Forms\Components\Select::make('doctor_id')
                            ->label(__('dentalink.fields.doctor'))
                            ->relationship('doctor', 'name')
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('lab_id')
                            ->label(__('dentalink.fields.lab'))
                            ->relationship('lab', 'name')
                            ->searchable()
                            ->required(),
                        Forms\Components\TextInput::make('service_name')
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options(collect(OrderStatus::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()]))
                            ->required(),
                        Forms\Components\TextInput::make('cost')
                            ->numeric()
                            ->prefix('$'),
                        Forms\Components\TextInput::make('commission')
                            ->numeric()
                            ->prefix('$'),
                        Forms\Components\TextInput::make('total')
                            ->numeric()
                            ->prefix('$'),
                        Forms\Components\DatePicker::make('expected_delivery_at'),
                        Forms\Components\Toggle::make('is_express'),
                        Forms\Components\Textarea::make('notes')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label(__('dentalink.fields.order_number'))
                    ->searchable()
                    ->formatStateUsing(fn (?string $state) => $state ? "#{$state}" : '—'),
                Tables\Columns\TextColumn::make('doctor.name')
                    ->label(__('dentalink.fields.doctor'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('lab.name')
                    ->label(__('dentalink.fields.lab'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('service_name')
                    ->label(__('dentalink.fields.service')),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (?OrderStatus $state) => $state?->label()),
                Tables\Columns\TextColumn::make('total')
                    ->money('usd'),
                Tables\Columns\TextColumn::make('created_at')
                    ->date('M j, Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(collect(OrderStatus::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()])),
                Tables\Filters\SelectFilter::make('doctor_id')
                    ->relationship('doctor', 'name')
                    ->label(__('dentalink.fields.doctor'))
                    ->searchable(),
                Tables\Filters\SelectFilter::make('lab_id')
                    ->relationship('lab', 'name')
                    ->label(__('dentalink.fields.lab'))
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            StagesRelationManager::class,
            LogsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
            'view' => Pages\ViewOrder::route('/{record}'),
        ];
    }
}
