<?php

namespace App\Filament\Lab\Resources;

use App\Enums\OrderStatus;
use App\Filament\Lab\Resources\OrderResource\Pages;
use App\Filament\Lab\Resources\OrderResource\RelationManagers\LogsRelationManager;
use App\Filament\Lab\Resources\OrderResource\RelationManagers\StagesRelationManager;
use App\Models\Order;
use App\Support\CurrentLab;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrderResource extends Resource
{
    public static function getNavigationGroup(): ?string
    {
        return __('dentalink.nav.groups.orders');
    }

    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    public static function getNavigationLabel(): string
    {
        return __('dentalink.resources.my_orders.nav');
    }

    

    protected static ?int $navigationSort = 1;

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
                Forms\Components\Section::make(__('dentalink.sections.order_status'))
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options(collect(OrderStatus::cases())->mapWithKeys(fn ($c) => [$c->value => $c->label()]))
                            ->required(),
                        Forms\Components\DatePicker::make('expected_delivery_at')
                            ->label(__('dentalink.fields.expected_delivery_at')),
                        Forms\Components\DatePicker::make('delivered_at')
                            ->label(__('dentalink.fields.delivered_at')),
                        Forms\Components\Textarea::make('notes')
                            ->rows(3)
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
                Tables\Columns\TextColumn::make('service_name')
                    ->label(__('dentalink.fields.service'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
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
                Tables\Columns\IconColumn::make('is_express')
                    ->boolean()
                    ->label(__('dentalink.fields.express')),
                Tables\Columns\TextColumn::make('cost')
                    ->money('usd'),
                Tables\Columns\TextColumn::make('expected_delivery_at')
                    ->label(__('dentalink.fields.due'))
                    ->date('M j, Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(collect(OrderStatus::cases())->mapWithKeys(fn ($c) => [$c->value => $c->label()])),
                Tables\Filters\TernaryFilter::make('is_express')
                    ->label(__('dentalink.filters.express_only')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('advance')
                    ->label(__('dentalink.actions.next_stage'))
                    ->icon('heroicon-o-arrow-right')
                    ->color('success')
                    ->visible(fn (Order $record) => ! in_array($record->status, [OrderStatus::Completed, OrderStatus::Cancelled, OrderStatus::Delivered], true))
                    ->action(function (Order $record) {
                        app(\App\Services\OrderWorkflowService::class)->advanceStage($record, auth()->id());
                        Notification::make()->title(__('dentalink.notifications.order_advanced'))->success()->send();
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('dentalink.sections.order_details'))
                    ->schema([
                        Infolists\Components\TextEntry::make('order_number')
                            ->formatStateUsing(fn (?string $state) => $state ? "#{$state}" : '—'),
                        Infolists\Components\TextEntry::make('status')
                            ->badge()
                            ->formatStateUsing(fn ($state) => $state?->label()),
                        Infolists\Components\TextEntry::make('doctor.name')
                            ->label(__('dentalink.fields.doctor')),
                        Infolists\Components\TextEntry::make('service_name')
                            ->label(__('dentalink.fields.service')),
                        Infolists\Components\TextEntry::make('tooth_number')
                            ->label(__('dentalink.fields.tooth_area_short')),
                        Infolists\Components\TextEntry::make('material'),
                        Infolists\Components\TextEntry::make('shade'),
                        Infolists\Components\IconEntry::make('is_express')
                            ->boolean()
                            ->label(__('dentalink.fields.express')),
                        Infolists\Components\TextEntry::make('expected_delivery_at')
                            ->date('M j, Y'),
                        Infolists\Components\TextEntry::make('cost')
                            ->money('usd')
                            ->label(__('dentalink.fields.your_fee')),
                        Infolists\Components\TextEntry::make('notes')
                            ->columnSpanFull()
                            ->placeholder(__('dentalink.common.no_notes')),
                    ])
                    ->columns(3),
            ]);
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
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
