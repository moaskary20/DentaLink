<?php

namespace App\Filament\App\Resources;

use App\Enums\OrderStatus;
use App\Filament\App\Resources\OrderResource\Pages;
use App\Models\Lab;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class OrderResource extends Resource
{
    public static function getNavigationGroup(): ?string
    {
        return __('dentalink.nav.groups.order_management');
    }

    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?int $navigationSort = 3;

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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('doctor_id', Auth::id());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('dentalink.sections.order_details'))
                    ->schema([
                        Forms\Components\TextInput::make('service_name')
                            ->label(__('dentalink.fields.service'))
                            ->required(),
                        Forms\Components\Select::make('lab_id')
                            ->label(__('dentalink.fields.laboratory'))
                            ->relationship('lab', 'name')
                            ->searchable()
                            ->required(),
                        Forms\Components\TextInput::make('tooth_number')
                            ->label(__('dentalink.fields.tooth_area')),
                        Forms\Components\Select::make('material')
                            ->options([
                                'Zirconia' => __('dentalink.materials.zirconia'),
                                'PFM' => __('dentalink.materials.pfm'),
                                'E-Max' => __('dentalink.materials.emax'),
                                'Acrylic' => __('dentalink.materials.acrylic'),
                            ]),
                        Forms\Components\Select::make('shade')
                            ->options([
                                'A1' => 'A1', 'A2' => 'A2', 'A3' => 'A3', 'B1' => 'B1', 'B2' => 'B2',
                            ]),
                        Forms\Components\DatePicker::make('expected_delivery_at')
                            ->label(__('dentalink.fields.expected_delivery_at')),
                        Forms\Components\Toggle::make('is_express')
                            ->label(__('dentalink.fields.express_order')),
                        Forms\Components\Textarea::make('notes')
                            ->rows(3),
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
                    ->sortable()
                    ->formatStateUsing(fn (?string $state) => $state ? "#{$state}" : __('dentalink.common.em_dash')),
                Tables\Columns\TextColumn::make('service_name')
                    ->label(__('dentalink.fields.service'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('lab.name')
                    ->label(__('dentalink.fields.lab'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('dentalink.fields.date'))
                    ->date('M j, Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (?OrderStatus $state) => $state?->label())
                    ->color(fn (?OrderStatus $state) => match ($state) {
                        OrderStatus::InProgress => 'info',
                        OrderStatus::QualityReview => 'warning',
                        OrderStatus::Shipping, OrderStatus::Delivered => 'primary',
                        OrderStatus::Completed => 'success',
                        OrderStatus::Cancelled => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('total')
                    ->money('usd')
                    ->sortable(),
                Tables\Columns\TextColumn::make('turnaround_days')
                    ->label(__('dentalink.fields.turnaround'))
                    ->suffix(' '.__('dentalink.units.days')),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(collect(OrderStatus::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()])),
                Tables\Filters\SelectFilter::make('lab_id')
                    ->label(__('dentalink.fields.lab'))
                    ->options(fn () => Lab::query()->pluck('name', 'id')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
        ];
    }
}
