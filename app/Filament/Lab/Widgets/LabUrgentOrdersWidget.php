<?php

namespace App\Filament\Lab\Widgets;

use App\Enums\OrderStatus;
use App\Filament\Lab\Resources\OrderResource;
use App\Models\Order;
use App\Support\CurrentLab;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LabUrgentOrdersWidget extends BaseWidget
{
    public function getHeading(): ?string
    {
        return __('dentalink.widgets.urgent_orders.heading');
    }

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $labId = CurrentLab::id();

        return $table
            ->query(
                Order::query()
                    ->with(['doctor'])
                    ->when($labId, fn ($q) => $q->where('lab_id', $labId))
                    ->when(! $labId, fn ($q) => $q->whereRaw('1 = 0'))
                    ->where(function ($q) {
                        $q->where('is_express', true)
                            ->orWhereIn('status', [OrderStatus::QualityReview, OrderStatus::Received]);
                    })
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->formatStateUsing(fn (?string $state) => $state ? "#{$state}" : '—'),
                Tables\Columns\TextColumn::make('doctor.name')
                    ->label(__('dentalink.fields.doctor')),
                Tables\Columns\TextColumn::make('service_name'),
                Tables\Columns\IconColumn::make('is_express')
                    ->boolean()
                    ->label(__('dentalink.fields.express')),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state?->label()),
                Tables\Columns\TextColumn::make('expected_delivery_at')
                    ->date('M j, Y')
                    ->label(__('dentalink.fields.due')),
            ])
            ->actions([
                Tables\Actions\Action::make('manage')
                    ->label(__('dentalink.actions.manage'))
                    ->icon('heroicon-o-wrench-screwdriver')
                    ->url(fn (Order $record) => OrderResource::getUrl('view', ['record' => $record])),
            ])
            ->paginated(false)
            ->emptyStateHeading(__('dentalink.widgets.empty.no_urgent_orders'));
    }
}
